<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\AttendanceCorrectionRequestController;

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

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendances.index');
    Route::post('attendance/clock-in', [AttendanceController::class, 'clockIn'])
        ->name('attendances.clock_in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
        ->name('attendances.clock_out');
    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])
        ->name('attendances.break_start');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])
        ->name('attendances.break_end');
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])
        ->name('attendances.show');
    Route::post('/attendance/{attendance}/correction-request', [AttendanceCorrectionRequestController::class, 'store'])
        ->name('attendance_correction_requests.store');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/attendances', [AdminAttendanceController::class, 'index'])
        ->name('admin.attendances.index');
});

Route::get('/test-user', function () {
    return '
        <h1>user dashboard</h1>
        <form method="POST" action="/logout">
            <input type="hidden" name="_token" value="'.csrf_token().'">
            <button type="submit">ログアウト</button>
        </form>
    ';
})->middleware(['auth', 'role:user']);

Route::get('/test-admin', function () {
    return '
        <h1>admin dashboard</h1>
        <form method="POST" action="/logout">
            <input type="hidden" name="_token" value="'.csrf_token().'">
            <button type="submit">ログアウト</button>
        </form>
    ';
})->middleware(['auth', 'role:admin']);

Route::get('/admin', function () {
    return 'admin dashboard';
})->middleware(['auth', 'role:admin'])->name('admin.index');