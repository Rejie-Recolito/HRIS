<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DtrController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceRecordController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\LeaveApplicationController;
use App\Livewire\LeaveApplicationView;
use Carbon\Carbon;

// DTR uploads list, view, and delete
Route::get('/admin/dtr/uploads', [\App\Http\Controllers\DtrController::class, 'uploadsList'])->middleware(['auth', 'verified'])->name('admin.dtr.uploads');
Route::get('/admin/dtr/uploads/{upload}', [\App\Http\Controllers\DtrController::class, 'viewUpload'])->middleware(['auth', 'verified'])->name('admin.dtr.uploads.view');
Route::delete('/admin/dtr/uploads/{upload}', [\App\Http\Controllers\DtrController::class, 'deleteUpload'])->middleware(['auth', 'verified'])->name('admin.dtr.uploads.delete');

// Store DTR data after preview (admin action)
Route::post('/admin/dtr/store/{upload}', [DtrController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.dtr.store');
// Admin: Service Record Requests History (unified)
Route::get('/service-records/history', [ServiceRecordController::class, 'historyIndex'])->middleware(['auth', 'verified'])->name('service-records.history');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard_user');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin-only employee management routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Check if user is admin before allowing access
    Route::get('/employees', function () {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        return app(EmployeeController::class)->index();
    })->name('employees.index');
    
    Route::get('/employees/{id}/edit', function ($id) {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        return app(EmployeeController::class)->edit($id);
    })->name('employees.edit');
    
    Route::put('/employees/{id}', function ($id) {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Allow users to update their own profile, or admins to update any profile
        $employee = \App\Models\Employee::findOrFail($id);
        if (!Auth::user()->is_admin && $employee->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return app(EmployeeController::class)->update(request(), $id);
    })->name('employees.update');
    
    Route::delete('/employees/{id}', function ($id) {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        return app(EmployeeController::class)->destroy($id);
    })->name('employees.destroy');
    
    Route::get('/employees/create', function () {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        return app(EmployeeController::class)->create();
    })->name('employees.create');
});

// User employee profile (store/update own profile)
Route::post('/employees', [EmployeeController::class, 'store'])->middleware(['auth', 'verified'])->name('employees.store');

Route::get('/service_record', function () {
    // Legacy admin route: redirect to the service record requests board
    return redirect()->route('service-record-requests.index');
})->name('service_record');

// Allow users to create a service record request (specific route must come before wildcard /service-records/{id})
Route::post('/service-records/request', [ServiceRecordController::class, 'requestByUser'])->name('service-records.request');

// Admin request board
Route::get('/service-record-requests', [ServiceRecordController::class, 'requestsIndex'])->name('service-record-requests.index');
Route::get('/service-record-requests/history', [ServiceRecordController::class, 'historyIndex'])->name('service-record-requests.history');
Route::post('/service-record-requests/{id}/accept', [ServiceRecordController::class, 'acceptRequest'])->name('service-record-requests.accept');
Route::get('/service-record-requests/{id}/process', [ServiceRecordController::class, 'showProcessing'])->name('service-record-requests.process');
Route::delete('/service-record-requests/{id}', [ServiceRecordController::class, 'destroyRequest'])->name('service-record-requests.destroy');

// NEW: Service record certification workflow routes
Route::get('/service-record-requests/{id}/verify', [ServiceRecordController::class, 'showVerification'])->middleware(['auth', 'verified'])->name('service-records.verify');
Route::post('/service-record-requests/{id}/mark-verified', [ServiceRecordController::class, 'markAsVerified'])->middleware(['auth', 'verified'])->name('service-records.mark-verified');
Route::post('/service-record-requests/{id}/generate-document', [ServiceRecordController::class, 'generateCertifiedDocument'])->middleware(['auth', 'verified'])->name('service-records.generate-document');
Route::post('/service-record-requests/{id}/certify', [ServiceRecordController::class, 'certifyDocument'])->middleware(['auth', 'verified'])->name('service-records.certify');
Route::get('/service-record-requests/{id}/download', [ServiceRecordController::class, 'downloadCertified'])->middleware(['auth', 'verified'])->name('service-records.download-certified');

// Admin edit/update routes for service records
Route::get('/service-records/{id}/edit', [ServiceRecordController::class, 'edit'])->name('service-records.edit');
Route::post('/service-records/{id}', [ServiceRecordController::class, 'update'])->name('service-records.update');
Route::post('/service-records/{id}/append', [ServiceRecordController::class, 'append'])->name('service-records.append');
Route::post('/service-records/{id}/accept', [ServiceRecordController::class, 'accept'])->name('service-records.accept');
Route::delete('/service-records/{id}', [ServiceRecordController::class, 'destroy'])->name('service-records.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin leave management
    Route::get('/leave', [\App\Http\Controllers\LeaveApplicationController::class, 'index'])->name('leave');
    Route::post('/leave/{id}/accept', [\App\Http\Controllers\LeaveApplicationController::class, 'accept'])->name('leave.accept');
    Route::post('/leave/{id}/approve', [\App\Http\Controllers\LeaveApplicationController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{id}/deny', [\App\Http\Controllers\LeaveApplicationController::class, 'deny'])->name('leave.deny');
    Route::post('/leave/{id}/action', [\App\Http\Controllers\LeaveApplicationController::class, 'storeAction'])->name('leave.action.update');
    Route::delete('/leave/{id}/delete', [\App\Http\Controllers\LeaveApplicationController::class, 'delete'])->name('leave.delete');
    Route::get('/leave/generateDocx/{id}', [\App\Http\Controllers\LeaveApplicationController::class, 'generateDocx'])->name('leave.generate-docx');
});

Route::get('/dtr', [DtrController::class, 'show'])->middleware(['auth', 'verified'])->name('dtr');

// Controller handles parsing, mapping, and streaming-friendly parsing
Route::post('/dtr/upload', [DtrController::class, 'upload'])->middleware(['auth', 'verified'])->name('dtr.upload');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
     // Admin dashboard route
     Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Admin user approval routes
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserApprovalController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/approve', [\App\Http\Controllers\Admin\UserApprovalController::class, 'approve'])->name('admin.users.approve');
    
    // Leave card (assign/view leave credits for employees)
    Route::get('/employees/{id}/leave-card', [\App\Http\Controllers\LeaveCreditController::class, 'show'])->name('employees.leave_card');
    Route::post('/employees/{id}/leave-card', [\App\Http\Controllers\LeaveCreditController::class, 'store'])->name('employees.leave_card.store');
    
    // Service Record routes for employees
    Route::get('/employees/{id}/service-record', [EmployeeController::class, 'showServiceRecord'])->name('employees.service_record');
    Route::post('/employees/{id}/service-record', [EmployeeController::class, 'storeServiceRecord'])->name('employees.service_record.store');
    Route::put('/employees/{employee}/service-record/{record}', [EmployeeController::class, 'updateServiceRecord'])->name('employees.service_record.update');
    Route::delete('/employees/{employee}/service-record/{record}', [EmployeeController::class, 'deleteServiceRecord'])->name('employees.service_record.delete');
     
     // User information/employee profile route (shows the form with existing data if present)
     Route::get('/add-user-information', [\App\Http\Controllers\EmployeeController::class, 'showUserForm'])
         ->name('add-user-information.user');
});

require __DIR__.'/auth.php';

// User-only routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/leave_user', [\App\Http\Controllers\LeaveApplicationController::class, 'create'])->name('leave.user');
    Route::post('/leave_user', [\App\Http\Controllers\LeaveApplicationController::class, 'store'])->name('leave.user.submit');
    Route::post('/leave_user/{id}/acknowledge', [\App\Http\Controllers\LeaveApplicationController::class, 'acknowledge'])->name('leave.user.acknowledge');

    Route::get('/service_record_user', [ServiceRecordController::class, 'show'])->name('service_record.user');

    Route::post('/service-records', [ServiceRecordController::class, 'store'])->name('service-records.store');
    Route::get('/service-records', [ServiceRecordController::class, 'index'])->name('service-records.index');
    Route::post('/service-records/{id}/update-status', [ServiceRecordController::class, 'updateStatus'])->name('service-records.update-status');
    Route::get('/service-record-user', [ServiceRecordController::class, 'show'])->name('service-records.show');
    Route::get('/add-user-information', [\App\Http\Controllers\EmployeeController::class, 'showUserForm'])
        ->name('add-user-information.user');
    
    // Export service records to DOCX for a user
    Route::get('/service-records/{user}/export', [ServiceRecordController::class, 'exportDocx'])->name('service-records.export');
});

Route::get('/service_record/{id}', function ($id) {
    $serviceRecord = \App\Models\ServiceRecord::findOrFail($id);
    return view('admin.request_form', compact('serviceRecord'));
})->name('service_record.request_form');

Route::post('/service_record/{id}/generate', function ($id) {
    $serviceRecord = \App\Models\ServiceRecord::findOrFail($id);

    $pdf = Pdf::loadView('admin.service_record_pdf', compact('serviceRecord'));
    return $pdf->download('service_record.pdf');
})->name('service_record.generate');

Route::post('/profile/picture', [ProfileController::class, 'uploadPicture'])->name('profile.picture.upload');

Route::get('/leave-application/{id}/view', \App\Livewire\LeaveApplicationView::class)
    ->name('leave_application.view');


Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave_applications.index');

// NEW: Route for users to mark service record request as claimed
Route::post('/service-records/{id}/mark-claimed', [ServiceRecordController::class, 'markAsClaimed'])->middleware(['auth', 'verified'])->name('service-records.mark-claimed');

// Route for AJAX trend data
Route::get('/admin/dashboard/trends', [App\Http\Controllers\AdminDashboardController::class, 'trends'])->name('admin.dashboard.trends');

