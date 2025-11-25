<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveCredit;
use App\Models\Employee;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class LeaveApplicationController extends Controller
{
    // Admin: approve a leave application and download PDF
    public $leaveApplications;
    public function approve($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            // Ensure consumption is performed (idempotent). If specific per-category
            // "less_this_application" fields are not set, fall back to the
            // request's number_of_days when appropriate.
            $this->performConsumption($leave);

            $leave->status = 'Approved';
            $leave->approved_at = now();
            $leave->action_date = now();
            $leave->save();
            
            // Notify the user who submitted the leave application
            $user = User::find($leave->user_id);
            if ($user) {
                $user->notifications()->create([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\Notifications\LeaveApproved',
                    'data' => [
                        'message' => "Your application for leave has been approved.\n \nYou may now download your Leave Application document sent on your email address.",
                        'leave_application_id' => $leave->id,
                    ],
                ]);
            }
            
            $this->leaveApplications = LeaveApplication::all();
            // After approving, generate the document and return it for download.
            try {
                return $this->generateDocx($leave->id);
            } catch (\Throwable $e) {
                // If generation fails, fall back to the previous behavior and surface an error message
                Log::error('Failed to generate leave application document after approval: ' . $e->getMessage());
                return redirect()->back()->with('success');
            }
        }
    }
    public function create()
    {
        $lastApplication = Auth::user()->leaveApplications()->latest()->first();
        return view('leave_user', compact('lastApplication'));
    }

    public function acknowledge($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        
        // Verify the leave belongs to the authenticated user
        if ($leave->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Mark the leave as acknowledged by storing it in session
        session()->put('acknowledged_leave_' . $id, true);
        
        return redirect()->route('leave.user');
    }

    public function deny($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review' || $leave->status === 'Approved') {
            // If already approved, restore consumed credits
            $this->restoreConsumedCredits($leave);

            $leave->status = 'Denied';
            $leave->action_date = now();
            $leave->save();
            
            // Notify the user who submitted the leave application
            $user = User::find($leave->user_id);
            if ($user) {
                $user->notifications()->create([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\Notifications\LeaveDenied',
                    'data' => [
                        'message' => "Your application for leave has been disapproved.\n\nYou may now download your Leave Application document sent on your email address to see details of action.",
                        'leave_application_id' => $leave->id,
                    ],
                ]);
            }
            
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
        $validated['status'] = 'Submitted';

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
                'id' => (string) Str::uuid(),
                'type' => AdminNotification::class,
                'data' => [
                    'message' => Auth::user()->name . ' has submitted application for leave.',
                    'leave_application_id' => $leaveApplication->id,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Leave application submitted successfully!');
    }

    // Admin: show all leave applications
    public function index(Request $request)
    {
        // Only show non-deleted applications in the main table
        $leaveApplications = LeaveApplication::where('is_deleted', false)->get();
        
        // History query with search/filter (includes all applications, even deleted ones)
        $query = LeaveApplication::with('user');

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $connectionDriver = DB::getDriverName();

            // SQLite doesn't support CONCAT(), use || for concatenation
            if ($connectionDriver === 'sqlite') {
                $concat1 = "firstname || ' ' || lastname";
                $concat2 = "lastname || ' ' || firstname";
            } else {
                $concat1 = "CONCAT(firstname, ' ', lastname)";
                $concat2 = "CONCAT(lastname, ' ', firstname)";
            }

            $query->where(function($q) use ($search, $concat1, $concat2) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('middlename', 'like', "%{$search}%")
                  ->orWhereRaw("{$concat1} like ?", ["%{$search}%"]) 
                  ->orWhereRaw("{$concat2} like ?", ["%{$search}%"]);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->filled('type')) {
            $query->where('type_of_leave', $request->type);
        }

        $historyApplications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.leave', compact('leaveApplications', 'historyApplications'));
    }

    // Admin: accept a leave application (for demo, just delete)
    public function accept($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Submitted') {
            $leave->status = 'Under Review';
            $leave->save();
            
            // Notify the user who submitted the leave application
            $user = User::find($leave->user_id);
            if ($user) {
                $user->notifications()->create([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\Notifications\LeaveUnderReview',
                    'data' => [
                        'message' => "Your submitted application for leave is currently under review.\n \nYou shall receive notification of approval or disapproval once your application has completed necessary actions.",
                        'leave_application_id' => $leave->id,
                    ],
                ]);
            }
            
            $this->leaveApplications = LeaveApplication::all();
            return redirect()->back()->with('success');
        }
    }

    // Admin: delete a leave application
    public function delete($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        // Only restore consumed credits if the leave was not approved
        // Approved leaves are considered completed, so credits should remain consumed
        if ($leave->status !== 'Approved') {
            $this->restoreConsumedCredits($leave);
        }
        // Mark as deleted instead of actually deleting
        $leave->is_deleted = true;
        $leave->save();
        return redirect()->route('leave')->with('success', 'Leave application deleted successfully.');
    }

    /**
     * Restore consumed credits that were created when a leave was approved.
     * This creates compensating positive LeaveCredit rows tied to the same leave application
     * to keep an audit trail instead of removing the original consumption rows.
     */
    protected function restoreConsumedCredits(LeaveApplication $leave)
    {
        if (! $leave->user_id) return;

        $employee = Employee::where('user_id', $leave->user_id)->first();
        if (! $employee) return;

        $consumptions = LeaveCredit::where('employee_id', $employee->id)
            ->where('source_type', LeaveApplication::class)
            ->where('source_id', $leave->id)
            ->where('amount', '<', 0)
            ->get();

        foreach ($consumptions as $c) {
            // skip if a reversal already exists for this source/type
            $exists = LeaveCredit::where('employee_id', $employee->id)
                ->where('source_type', LeaveApplication::class)
                ->where('source_id', $leave->id)
                ->where('type', $c->type)
                ->where('amount', '>', 0)
                ->where('notes', 'like', '%Reversal for leave_application:%')
                ->exists();

            if ($exists) continue;

            LeaveCredit::create([
                'employee_id' => $employee->id,
                'type' => $c->type,
                'amount' => -1 * $c->amount, // negate the negative consumption -> positive restore
                'assigned_by' => Auth::id(),
                'notes' => 'Reversal for leave_application:' . $leave->id,
                'source_type' => LeaveApplication::class,
                'source_id' => $leave->id,
            ]);
        }
    }

    /**
     * Perform consumption of leave credits for the given leave application.
     * Creates negative LeaveCredit entries for vacation and/or sick as needed.
     * Idempotent: it won't create duplicate consumption rows for the same leave.
     */
    protected function performConsumption(LeaveApplication $leave)
    {
        if (! $leave->user_id) {
            return;
        }

        $employee = Employee::where('user_id', $leave->user_id)->first();
        if (! $employee) {
            return;
        }

        $marker = 'leave_application:' . $leave->id;

        // Determine how many days to consume per category.
        // Prefer explicit per-category fields. If neither explicit field is set,
        // only apply the fallback number_of_days to the category that matches
        // the leave's type (e.g. 'Vacation leave' -> vacation). This avoids
        // consuming sick leave when the user did not request sick leave.
        $fallbackDays = (int) ($leave->number_of_days ?? 0);

        $vacationExplicit = isset($leave->vacation_less_this_application) && $leave->vacation_less_this_application !== null;
        $sickExplicit = isset($leave->sick_less_this_application) && $leave->sick_less_this_application !== null;

        $appliesVacation = false;
        $appliesSick = false;

        if ($vacationExplicit) {
            $appliesVacation = true;
        }
        if ($sickExplicit) {
            $appliesSick = true;
        }

        if (! $vacationExplicit && ! $sickExplicit) {
            // decide based on leave type string
            $type = strtolower((string) $leave->type_of_leave);
            if (strpos($type, 'vacation') !== false) {
                $appliesVacation = true;
            } elseif (strpos($type, 'sick') !== false) {
                $appliesSick = true;
            } else {
                // unknown/other leave type: default to vacation only (safer)
                $appliesVacation = true;
            }
        }

        $consumedVac = $vacationExplicit ? (int) $leave->vacation_less_this_application : ($appliesVacation ? $fallbackDays : 0);
        $consumedSick = $sickExplicit ? (int) $leave->sick_less_this_application : ($appliesSick ? $fallbackDays : 0);

        // Vacation consumption
        if ($consumedVac > 0) {
            $exists = LeaveCredit::where('employee_id', $employee->id)
                ->where('source_type', LeaveApplication::class)
                ->where('source_id', $leave->id)
                ->where('type', 'vacation')
                ->exists();

            if (! $exists) {
                LeaveCredit::create([
                    'employee_id' => $employee->id,
                    'type' => 'vacation',
                    'amount' => -1 * $consumedVac,
                    'assigned_by' => Auth::id(),
                    'notes' => 'Consumed for ' . $marker,
                    'source_type' => LeaveApplication::class,
                    'source_id' => $leave->id,
                ]);
            }
        }

        // Sick consumption
        if ($consumedSick > 0) {
            $exists = LeaveCredit::where('employee_id', $employee->id)
                ->where('source_type', LeaveApplication::class)
                ->where('source_id', $leave->id)
                ->where('type', 'sick')
                ->exists();

            if (! $exists) {
                LeaveCredit::create([
                    'employee_id' => $employee->id,
                    'type' => 'sick',
                    'amount' => -1 * $consumedSick,
                    'assigned_by' => Auth::id(),
                    'notes' => 'Consumed for ' . $marker,
                    'source_type' => LeaveApplication::class,
                    'source_id' => $leave->id,
                ]);
            }
        }
    }

    public function downloadAllPdf()
    {
        $leaveApplications = LeaveApplication::all(); // Fetch all leave applications
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_applications', compact('leaveApplications'));
        return $pdf->download('leave_applications.pdf');
    }

    

   public function generateDocx($id)
{
    // Load the template
    $templatePath = storage_path('app/templates/leave_application_template.docx');
    if (!file_exists($templatePath)) {
        throw new \Exception('Template file not found at: ' . $templatePath);
    }

    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
    $leaveApplication = \App\Models\LeaveApplication::findOrFail($id);

    // Perform consumption when generating the document. This is idempotent
    // so repeated downloads won't duplicate consumption.
    if (method_exists($this, 'performConsumption')) {
        $this->performConsumption($leaveApplication);
    }

    // Fill placeholders
    $templateProcessor->setValue('lastname', $leaveApplication->lastname);
    $templateProcessor->setValue('firstname', $leaveApplication->firstname);
    $templateProcessor->setValue('middlename', $leaveApplication->middlename);
    $templateProcessor->setValue('date_of_filing', $leaveApplication->date_of_filing);
    $templateProcessor->setValue('position', $leaveApplication->position);
    $templateProcessor->setValue('salary', $leaveApplication->salary);
    $templateProcessor->setValue('department', $leaveApplication->department);
    $templateProcessor->setValue('number_of_days', $leaveApplication->number_of_days);
    $templateProcessor->setValue('inclusive_dates', $leaveApplication->inclusive_dates);
    $templateProcessor->setValue('others', $leaveApplication->others ?? '');

    // New admin/action fields
    $templateProcessor->setValue('cert_as_of', $leaveApplication->cert_as_of ? $leaveApplication->cert_as_of->format('Y-m-d') : '');
    // Recommendation checkboxes: use separate placeholders so one doesn't overwrite the other
    $templateProcessor->setValue('recommendation_for_approval', $leaveApplication->recommendation === 'For approval' ? '☑' : '☐');
    $templateProcessor->setValue('recommendation_for_disapproval', $leaveApplication->recommendation === 'For disapproval' ? '☑' : '☐');
    $templateProcessor->setValue('recommendation_reason', $leaveApplication->recommendation_reason ?? '');
    $templateProcessor->setValue('authorized_officer_leave_cred', $leaveApplication->authorized_officer_leave_cred ?? '');
    $templateProcessor->setValue('authorized_officer_recommendation', $leaveApplication->authorized_officer_recommendation ?? '');
    // Separate vacation/sick placeholders
    $templateProcessor->setValue('vacation_total_earned', isset($leaveApplication->vacation_total_earned) ? $leaveApplication->vacation_total_earned : '');
    $templateProcessor->setValue('vacation_less_this_application', isset($leaveApplication->vacation_less_this_application) ? $leaveApplication->vacation_less_this_application : '');
    $templateProcessor->setValue('vacation_balance', isset($leaveApplication->vacation_balance) ? $leaveApplication->vacation_balance : '');
    $templateProcessor->setValue('sick_total_earned', isset($leaveApplication->sick_total_earned) ? $leaveApplication->sick_total_earned : '');
    $templateProcessor->setValue('sick_less_this_application', isset($leaveApplication->sick_less_this_application) ? $leaveApplication->sick_less_this_application : '');
    $templateProcessor->setValue('sick_balance', isset($leaveApplication->sick_balance) ? $leaveApplication->sick_balance : '');
    $templateProcessor->setValue('approved_days_with_pay', $leaveApplication->approved_days_with_pay ?? '');
    $templateProcessor->setValue('approved_days_without_pay', $leaveApplication->approved_days_without_pay ?? '');
    $templateProcessor->setValue('approved_others', $leaveApplication->approved_others ?? '');
    $templateProcessor->setValue('disapproved_reason', $leaveApplication->disapproved_reason ?? '');
    $templateProcessor->setValue('authorized_officer', $leaveApplication->authorized_officer ?? '');
    $templateProcessor->setValue('action_date', $leaveApplication->action_date ? $leaveApplication->action_date->format('Y-m-d') : '');

    // Checkbox logic
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

    // Save DOCX to a temporary file (do not persist to storage)
    $tempDocx = tempnam(sys_get_temp_dir(), 'leaveapp_') . '.docx';
    $templateProcessor->saveAs($tempDocx);

    // Convert DOCX → PDF using LibreOffice into the system temp dir
    $pdfTempDir = sys_get_temp_dir();
    $pdfTemp = $pdfTempDir . DIRECTORY_SEPARATOR . pathinfo($tempDocx, PATHINFO_FILENAME) . '.pdf';

    // 🔍 Detect OS and set LibreOffice path accordingly
    if (stripos(PHP_OS, 'WIN') === 0) {
        // Windows default path
        $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
    } elseif (stripos(PHP_OS, 'DAR') === 0) {
        // macOS (Homebrew or app path)
        $libreOfficePath = '/Applications/LibreOffice.app/Contents/MacOS/soffice';
        if (!file_exists($libreOfficePath)) {
            $libreOfficePath = 'libreoffice'; // fallback if in PATH
        }
    } else {
        // Linux or others
        $libreOfficePath = 'libreoffice';
    }

    $command = $libreOfficePath . ' --headless --convert-to pdf --outdir ' . escapeshellarg($pdfTempDir) . ' ' . escapeshellarg($tempDocx);
    exec($command, $output, $resultCode);

    if ($resultCode !== 0) {
        // cleanup temp docx
        if (file_exists($tempDocx)) @unlink($tempDocx);
        return redirect()->back()->with('error', 'Failed to convert DOCX to PDF');
    }

    // Wait briefly for output and verify PDF exists
    if (!file_exists($pdfTemp)) {
        // cleanup temp docx
        if (file_exists($tempDocx)) @unlink($tempDocx);
        return redirect()->back()->with('error', 'PDF conversion completed but PDF not found.');
    }



    // Email the generated PDF to the employee, then delete temporary files
    try {
        $user = User::find($leaveApplication->user_id);
        // Prefer employee.email_address when available
        $employee = Employee::where('user_id', $leaveApplication->user_id)->first();

        $candidateEmail = null;
        if ($employee && !empty($employee->email_address) && filter_var($employee->email_address, FILTER_VALIDATE_EMAIL)) {
            $candidateEmail = $employee->email_address;
        } elseif ($user && !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $candidateEmail = $user->email;
        }

        if ($candidateEmail) {
            $email = $candidateEmail;
            $subject = 'Leave Application Document';
            $body = "Your leave application (ID: {$leaveApplication->id}) has been processed. Please find the attached document.";
            $fileName = 'Leave_Application_' . $leaveApplication->id . '.pdf';

            Mail::raw($body, function ($message) use ($email, $subject, $pdfTemp, $fileName) {
                $message->to($email)
                    ->subject($subject);

                if (file_exists($pdfTemp)) {
                    $message->attach($pdfTemp, [
                        'as' => $fileName,
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            // Clean up temp files
            if (file_exists($pdfTemp)) {
                @unlink($pdfTemp);
            }
            if (file_exists($tempDocx)) {
                @unlink($tempDocx);
            }

            return redirect()->back()->with('success', 'Leave approved and document emailed to the employee.');
        }

        // If user/email not found, return an error to admin
        // Clean up files anyway
        if (file_exists($pdfTemp)) {
            @unlink($pdfTemp);
        }
        if (file_exists($tempDocx)) {
            @unlink($tempDocx);
        }

        return redirect()->back()->with('error', 'Leave approved but failed to send email: user email not found.');
    } catch (\Throwable $e) {
        Log::error('Failed to email leave application PDF: ' . $e->getMessage());
        // Clean up files
        if (file_exists($pdfTemp)) {
            @unlink($pdfTemp);
        }
        if (file_exists($tempDocx)) {
            @unlink($tempDocx);
        }

        return redirect()->back()->with('error', 'Leave approved but failed to send email: ' . $e->getMessage());
    }
}


  public function view($id)
{
    $leaveApplication = LeaveApplication::findOrFail($id);
    return view('livewire.leave-application-view', compact('leaveApplication'));
}

    /**
     * Store admin action fields for a leave application.
     */
    public function storeAction(Request $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $baseRules = [
            'cert_as_of' => 'nullable|date',
            'cert_vacation' => 'nullable|string|max:255',
            'cert_sick' => 'nullable|string|max:255',
            'authorized_officer_leave_cred' => 'nullable|string|max:255',
            'authorized_officer_recommendation' => 'nullable|string|max:255',
            'recommendation' => 'nullable|string|max:255',
            'recommendation_reason' => 'nullable|string',
            'approved_days_with_pay' => 'nullable|string|max:255',
            'approved_days_without_pay' => 'nullable|string|max:255',
            'approved_others' => 'nullable|string|max:255',
            'disapproved_reason' => 'nullable|string',
            'authorized_officer' => 'nullable|string|max:255',
            'action_date' => 'nullable|date',
            'inclusive_from' => 'nullable|date',
            'inclusive_to' => 'nullable|date',
            'cert_leave_type' => 'nullable|string|in:vacation,sick',
        ];

        $vacationRules = [
            'vacation_total_earned' => 'nullable|integer',
            'vacation_less_this_application' => 'nullable|integer',
            'vacation_balance' => 'nullable|integer',
        ];

        $sickRules = [
            'sick_total_earned' => 'nullable|numeric',
            'sick_less_this_application' => 'nullable|numeric',
            'sick_balance' => 'nullable|numeric',
        ];

        // Merge rules depending on what was submitted
        $rules = $baseRules;
        if ($request->input('cert_leave_type') === 'vacation') {
            $rules = array_merge($rules, $vacationRules);
        } elseif ($request->input('cert_leave_type') === 'sick') {
            $rules = array_merge($rules, $sickRules);
        } else {
            // allow legacy fields as fallback
            $rules = array_merge($rules, [
                'total_earned' => 'nullable|integer',
                'less_this_application' => 'nullable|integer',
                'balance' => 'nullable|integer',
            ]);
        }

        $validated = $request->validate($rules);

        // Fill base fields
    $leave->fill(collect($validated)->only(array_keys($baseRules))->toArray());

        // Fill appropriate credit group (we'll compute balances server-side)
        if ($request->input('cert_leave_type') === 'vacation' || isset($validated['vacation_total_earned']) || isset($validated['vacation_less_this_application'])) {
            // determine total earned: prefer validated value, fall back to existing leave record or employee leave credits
            $totalVacation = isset($validated['vacation_total_earned']) ? (int) $validated['vacation_total_earned'] : ($leave->vacation_total_earned ?? 0);

            // if there's an associated employee, derive current total from leave_credits
            if ($leave->user_id) {
                $employee = Employee::where('user_id', $leave->user_id)->first();
                if ($employee) {
                    $credits = LeaveCredit::where('employee_id', $employee->id)->get();
                    $totalVacation = (int) $credits->where('type', 'vacation')->sum('amount');
                }
            }

            $lessVacation = isset($validated['vacation_less_this_application']) ? (int) $validated['vacation_less_this_application'] : ($leave->vacation_less_this_application ?? 0);
            $balanceVacation = max(0, $totalVacation - $lessVacation);

            $leave->vacation_total_earned = $totalVacation;
            $leave->vacation_less_this_application = $lessVacation;
            $leave->vacation_balance = $balanceVacation;
        }

        if ($request->input('cert_leave_type') === 'sick' || isset($validated['sick_total_earned']) || isset($validated['sick_less_this_application'])) {
            $totalSick = isset($validated['sick_total_earned']) ? (int) $validated['sick_total_earned'] : ($leave->sick_total_earned ?? 0);

            if ($leave->user_id) {
                $employee = Employee::where('user_id', $leave->user_id)->first();
                if ($employee) {
                    $credits = LeaveCredit::where('employee_id', $employee->id)->get();
                    $totalSick = (int) $credits->where('type', 'sick')->sum('amount');
                }
            }

            $lessSick = isset($validated['sick_less_this_application']) ? (int) $validated['sick_less_this_application'] : ($leave->sick_less_this_application ?? 0);
            $balanceSick = max(0, $totalSick - $lessSick);

            $leave->sick_total_earned = $totalSick;
            $leave->sick_less_this_application = $lessSick;
            $leave->sick_balance = $balanceSick;
        }

        // Legacy fallback
        if (isset($validated['total_earned']) || isset($validated['less_this_application']) || isset($validated['balance'])) {
            $leave->total_earned = $validated['total_earned'] ?? $leave->total_earned;
            $leave->less_this_application = $validated['less_this_application'] ?? $leave->less_this_application;
            $leave->balance = $validated['balance'] ?? $leave->balance;
        }

        $leave->save();

        return redirect()->back()->with('success', 'Action details saved.');
    }

}
