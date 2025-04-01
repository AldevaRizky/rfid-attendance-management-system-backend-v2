<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/employee/dashboard', [DashboardController::class, 'employee'])->name('employee.dashboard');
    });

    // Redirect based on role after login
    Route::get('/dashboard', function () {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard');
    })->name('dashboard');
});
