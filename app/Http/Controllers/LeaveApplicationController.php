<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class LeaveApplicationController extends Controller
{
    // Admin: approve a leave application and download PDF
    public $leaveApplications;
    public function approve($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Approved';
            $leave->save();
            $this->leaveApplications = LeaveApplication::all();
            return redirect()->back()->with('success');
        }
    }
    public function create()
    {
        $lastApplication = Auth::user()->leaveApplications()->latest()->first();
        return view('leave_user', compact('lastApplication'));
    }

    public function deny($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Denied';
            $leave->save();
            $this->leaveApplications = LeaveApplication::all();
            return redirect()->back()->with('success');
        }
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'date_of_filing' => 'required|date',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'type_of_leave' => 'required|string',
            'others' => 'nullable|string|max:255',
            'number_of_days' => 'required|integer|min:1',
            'inclusive_dates' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'commutation' => 'required|string|max:255',
            'inCaseVacation' => 'nullable|string|max:255',
            'inCaseSick' => 'nullable|string|max:255',
            'inHospital' => 'nullable|string|max:255',
            'outPatient' => 'nullable|string|max:255',
            'inCaseSpecialLeaveBenefits' => 'nullable|string|max:255',
            'inCaseStudyLeave' => 'nullable|string|max:255',
            'withinPhilippines' => 'nullable|string|max:255',
            'abroad' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        // Check if the user has a pending leave application
        $existingApplication = LeaveApplication::where('user_id', Auth::id())
            ->whereIn('status', ['Under Review', 'Submitted'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You already have a leave application under review or submitted.');
        }

        $leaveApplication = LeaveApplication::create($validated);

        // Notify admin with a custom message
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            
            $admin->notifications()->create([
                'type' => AdminNotification::class,
                'data' => [
                    'message' => Auth::user()->name . ' has requested a leave.',
                    'leave_application_id' => $leaveApplication->id,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Leave application submitted successfully!');
    }

    // Admin: show all leave applications
    public function index()
    {
        $leaveApplications = LeaveApplication::all();
        return view('admin.leave', compact('leaveApplications'));
    }

    // Admin: accept a leave application (for demo, just delete)
    public function accept($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Submitted') {
            $leave->status = 'Under Review';
            $leave->save();
            $this->leaveApplications = LeaveApplication::all();
            return redirect()->back()->with('success');
        }
    }

    // Admin: delete a leave application
    public function delete($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->delete();
        return view('admin.leave')->with('success', 'Leave application deleted successfully.');
    }

    public function downloadAllPdf()
    {
        $leaveApplications = LeaveApplication::all(); // Fetch all leave applications
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_applications', compact('leaveApplications'));
        return $pdf->download('leave_applications.pdf');
    }

    public function downloadPdf($id)
    {
        // Fetch the specific leave application by ID
        $leave = LeaveApplication::findOrFail($id);

        // Generate the PDF using the view and the leave application data
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_applications', compact('leave'));

        // Return the PDF for download
        return $pdf->download('leave_application_' . $id . '.pdf');
    }

    public function generateDocx($id)
    {
        // Load the template
        if (!file_exists(storage_path('app/templates/leave_application_template.docx'))) {
            throw new \Exception('Template file not found at: ' . storage_path('app/templates/leave_application_template.docx'));
        }
        $templateProcessor = new TemplateProcessor(storage_path('app/templates/leave_application_template.docx'));

        // Fetch data from the database
        $leaveApplication = LeaveApplication::findOrFail($id);

        // Replace placeholders with actual data
        $templateProcessor->setValue('lastname', $leaveApplication->lastname);
        $templateProcessor->setValue('firstname', $leaveApplication->firstname);
        $templateProcessor->setValue('middlename', $leaveApplication->middlename);
        $templateProcessor->setValue('date_of_filing', $leaveApplication->date_of_filing);
        $templateProcessor->setValue('position', $leaveApplication->position);
        $templateProcessor->setValue('salary', $leaveApplication->salary);
        $templateProcessor->setValue('department', $leaveApplication->department);
        // Set checkboxes based on type_of_leave
        $templateProcessor->setValue('vacation_leave', $leaveApplication->type_of_leave === 'Vacation leave' ? '☑' : '☐');
        $templateProcessor->setValue('mandatory_leave', $leaveApplication->type_of_leave === 'Mandatory/Forced leave' ? '☑' : '☐');
        $templateProcessor->setValue('sick_leave', $leaveApplication->type_of_leave === 'Sick leave' ? '☑' : '☐');
        $templateProcessor->setValue('maternity_leave', $leaveApplication->type_of_leave === 'Maternity leave' ? '☑' : '☐');
        $templateProcessor->setValue('paternity_leave', $leaveApplication->type_of_leave === 'Paternity leave' ? '☑' : '☐');
        $templateProcessor->setValue('special_privilege_leave', $leaveApplication->type_of_leave === 'Special Privilege Leave' ? '☑' : '☐');
        $templateProcessor->setValue('solo_parent_leave', $leaveApplication->type_of_leave === 'Solo Parent leave' ? '☑' : '☐');
        $templateProcessor->setValue('study_leave', $leaveApplication->type_of_leave === 'Study leave' ? '☑' : '☐');
        $templateProcessor->setValue('10_day_vawc_leave', $leaveApplication->type_of_leave === '10-Day VAWC leave' ? '☑' : '☐');
        $templateProcessor->setValue('rehabilitation_privilege', $leaveApplication->type_of_leave === 'Rehabilitation Privilege' ? '☑' : '☐');
        $templateProcessor->setValue('special_leave_benefits_for_women', $leaveApplication->type_of_leave === 'Special Leave Benefits for Women' ? '☑' : '☐');
        $templateProcessor->setValue('special_emergency_calamity_leave', $leaveApplication->type_of_leave === 'Special Emergency(Calamity) Leave' ? '☑' : '☐');
        $templateProcessor->setValue('adoption_leave', $leaveApplication->type_of_leave === 'Adoption Leave' ? '☑' : '☐');
        $templateProcessor->setValue('others', $leaveApplication->others ?? '');
        $templateProcessor->setValue('number_of_days', $leaveApplication->number_of_days);
        $templateProcessor->setValue('inclusive_dates', $leaveApplication->inclusive_dates);

        //vacation details
        $templateProcessor->setValue('checkPhilippines', $leaveApplication->inCaseVacation === 'Within the Philippines' ? '☑' : '☐');
        $templateProcessor->setValue('checkAbroad', $leaveApplication->inCaseVacation === 'Abroad' ? '☑' : '☐');
        $templateProcessor->setValue('withinPhilippines', $leaveApplication->withinPhilippines);
        $templateProcessor->setValue('abroad', $leaveApplication->abroad);

        // Sick leave details
        $templateProcessor->setValue('checkInHospital', $leaveApplication->inCaseSick === 'In Hospital' ? '☑' : '☐');
        $templateProcessor->setValue('checkOutPatient', $leaveApplication->inCaseSick === 'Out Patient' ? '☑' : '☐');
        $templateProcessor->setValue('inHospital', $leaveApplication->inHospital);
        $templateProcessor->setValue('outPatient', $leaveApplication->outPatient);

        // Special Leave Benefits for Women
        $templateProcessor->setValue('sPLBW', $leaveApplication->inCaseSpecialLeaveBenefits);

        // Study Leave
        $templateProcessor->setValue('checkCMD', $leaveApplication->inCaseStudyLeave === 'Completion of Master\'s Degree' ? '☑' : '☐');
        $templateProcessor->setValue('checkBAR', $leaveApplication->inCaseStudyLeave === 'BAR/Board Examination Review' ? '☑' : '☐');
        $templateProcessor->setValue('checkTL', $leaveApplication->inCaseStudyLeave === 'Terminal Leave' ? '☑' : '☐');
        $templateProcessor->setValue('checkMLC', $leaveApplication->inCaseStudyLeave === 'Monetization of Leave Credits' ? '☑' : '☐');

        // Commutation
        $templateProcessor->setValue('checkNotRequested', $leaveApplication->commutation === 'Not Requested' ? '☑' : '☐');
        $templateProcessor->setValue('checkRequested', $leaveApplication->commutation === 'Requested' ? '☑' : '☐');

        // Save the populated file
        $fileName = 'Leave_Application_' . $leaveApplication->id . '.docx';
        $filePath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($filePath);

        // Return the file as a download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

  public function view($id)
{
    $leaveApplication = LeaveApplication::findOrFail($id);
    return view('livewire.leave-application-view', compact('leaveApplication'));
}

}
