<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\AttendanceCorrectionRequestController;
use App\Http\Controllers\Admin\CorrectionRequestController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

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

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendances.index');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendances.list');
    Route::get('/attendance/requests', [AttendanceCorrectionRequestController::class, 'index'])
        ->name('attendance_correction_requests.index');
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
    Route::get('/staff/list', [StaffController::class, 'index'])
        ->name('admin.staff.list');
    Route::get('/admin/correction-requests', [CorrectionRequestController::class, 'index'])
        ->name('admin.correction-requests.index');
    Route::get('/admin/correction-requests/{correctionRequest}', [CorrectionRequestController::class, 'show'])
        ->name('admin.correction-requests.show');
    Route::get('/attendance/staff/{user}', [StaffController::class, 'show'])
        ->name('admin.staff.attendances');
    Route::get('/attendance/staff/{user}/csv', [StaffController::class, 'exportCsv'])
        ->name('admin.staff.attendances.csv');
    Route::get('attendances/{attendance}', [AdminAttendanceController::class, 'show'])
        ->name('admin.attendances.show');
    Route::get('/attendances/{attendance}/edit', [AdminAttendanceController::class, 'edit'])
        ->name('admin.attendances.edit');
    Route::put('/attendances/{attendance}', [AdminAttendanceController::class, 'update'])
        ->name('admin.attendances.update');
    Route::post('/admin/correction-requests/{correctionRequest}/approve', [CorrectionRequestController::class, 'approve'])
        ->name('admin.correction-requests.approve');
    Route::post('/admin/correction-requests/{correctionRequest}/reject', [CorrectionRequestController::class, 'reject'])
        ->name('admin.correction-requests.reject');
});

Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])
    ->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/logout', function () {
    $redirectTo = auth()->user()?->role === 'admin'
        ? '/admin/login'
        : '/login';

    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect($redirectTo);
})->name('logout');