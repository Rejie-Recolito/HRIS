<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/employees', function () {
    return view('employees');
})->middleware(['auth', 'verified'])->name('employees');

Route::get('/service_record', function () {
    return view('service_record');
})->middleware(['auth', 'verified'])->name('service_record');

Route::get('/leave', function () {
    return view('leave');
})->middleware(['auth', 'verified'])->name('leave');

Route::get('/dtr', function () {
    return view('dtr');
})->middleware(['auth', 'verified'])->name('dtr');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
