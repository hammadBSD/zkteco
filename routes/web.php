<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return redirect('/attendance');
});

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/fetch-today', [AttendanceController::class, 'fetchToday'])->name('attendance.fetch-today');
Route::post('/attendance/fetch-limited/{limit}', [AttendanceController::class, 'fetchLimited'])->name('attendance.fetch-limited');
Route::get('/settings', [AttendanceController::class, 'settings'])->name('settings.index');