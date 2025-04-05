<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/leave-details/{userId}', [DashboardController::class, 'getLeaveDetails'])->name('leave.details');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
        Route::get('/attendance/leave-details/{userId}/{date}', [AttendanceController::class, 'getLeaveDetails'])
        ->name('attendance.leave.details');
        Route::get('/attendance/export-pdf', [AttendanceController::class, 'exportPDF'])
            ->name('attendance.export.pdf');
        Route::get('/attendance/export/excel', [AttendanceController::class, 'exportExcel'])->name('attendance.export.excel');

        // Division Management
        Route::resource('divisions', \App\Http\Controllers\Admin\DivisionController::class)->except(['show']);
        Route::resource('positions', \App\Http\Controllers\Admin\PositionController::class)->except(['show']);
        Route::resource('educations', \App\Http\Controllers\Admin\EducationController::class)->except(['show']);
        Route::resource('shifts', \App\Http\Controllers\Admin\ShiftController::class)->except(['show']);
        Route::resource('rfid-cards', \App\Http\Controllers\Admin\RfidCardController::class)->except(['show']);
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class)->except(['show']);
        Route::resource('admins', \App\Http\Controllers\Admin\AdminController::class)->except(['show']);
    });

    // Employee routes
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'employee'])->name('dashboard');

        // Route untuk form pengajuan izin
        Route::get('/leave-request/create', [App\Http\Controllers\Employee\LeaveRequestController::class, 'create'])->name('leave-request.create');
        Route::post('/leave-request', [App\Http\Controllers\Employee\LeaveRequestController::class, 'store'])->name('leave-request.store');
        Route::get('/attendance-history', [App\Http\Controllers\Employee\AttendanceHistoryController::class, 'index'])->name('attendance.history');
        Route::get('/leave-requests/{date}', [\App\Http\Controllers\Employee\AttendanceHistoryController::class, 'getLeaveRequest'])
    ->name('leave-requests.show');
    });


    // Redirect based on role after login
    Route::get('/dashboard', function () {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard');
    })->name('dashboard');
});
