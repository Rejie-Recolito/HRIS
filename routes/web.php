
<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceRecordController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\LeaveApplicationController;
use App\Livewire\LeaveApplicationView;

Route::post('/add-user-information/update', [EmployeeController::class, 'updateUserInfo'])->name('employee.updateUserInfo');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard_user');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

Route::get('/service_record', function () {
    return view('admin.service_record');
})->name('service_record');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin leave management
    Route::get('/leave', [\App\Http\Controllers\LeaveApplicationController::class, 'index'])->name('leave');
    Route::post('/leave/{id}/accept', [\App\Http\Controllers\LeaveApplicationController::class, 'accept'])->name('leave.accept');
    Route::post('/leave/{id}/approve', [\App\Http\Controllers\LeaveApplicationController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{id}/deny', [\App\Http\Controllers\LeaveApplicationController::class, 'deny'])->name('leave.deny');
    Route::delete('/leave/{id}/delete', [\App\Http\Controllers\LeaveApplicationController::class, 'delete'])->name('leave.delete');
    Route::get('/leave/generateDocx/{id}', [\App\Http\Controllers\LeaveApplicationController::class, 'generateDocx'])->name('leave.generate-docx');
});

Route::get('/dtr', function () {
    return view('admin.dtr');
})->middleware(['auth', 'verified'])->name('dtr');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
     // Admin dashboard route
     Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
     
     // User information/employee profile route
     Route::get('/add-user-information', [\App\Http\Controllers\EmployeeController::class, 'showUserForm'])->name('add-user-information.user');
});

require __DIR__.'/auth.php';

// User-only routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/leave_user', [\App\Http\Controllers\LeaveApplicationController::class, 'create'])->name('leave.user');
    Route::post('/leave_user', [\App\Http\Controllers\LeaveApplicationController::class, 'store'])->name('leave.user.submit');

    Route::get('/service_record_user', function () {
        return view('service_record_user');
    })->name('service_record.user');

    Route::post('/service-records', [ServiceRecordController::class, 'store'])->name('service-records.store');
    Route::get('/service-records', [ServiceRecordController::class, 'index'])->name('service-records.index');
    Route::post('/service-records/{id}/update-status', [ServiceRecordController::class, 'updateStatus'])->name('service-records.update-status');
    Route::get('/service-record-user', [ServiceRecordController::class, 'show'])->name('service-records.show');
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
