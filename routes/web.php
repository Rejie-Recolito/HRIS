<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard_user');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/employees', function () {
    return view('admin.employees');
})->middleware(['auth', 'verified'])->name('employees');

Route::get('/service_record', function () {
    return view('admin.service_record');
})->middleware(['auth', 'verified'])->name('service_record');

Route::get('/leave', function () {
    return view('admin.leave');
})->middleware(['auth', 'verified'])->name('leave');

Route::get('/dtr', function () {
    return view('admin.dtr');
})->middleware(['auth', 'verified'])->name('dtr');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
     // Admin dashboard route
     Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
});



require __DIR__.'/auth.php';

// User-only routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/leave_user', [\App\Http\Controllers\LeaveApplicationController::class, 'create'])->name('leave.user');
    Route::post('/leave_user', [\App\Http\Controllers\LeaveApplicationController::class, 'store'])->name('leave.user.submit');

    Route::get('/service_record_user', function () {
        return view('service_record_user');
    })->name('service_record.user');
});
