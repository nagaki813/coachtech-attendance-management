<?php

use Illuminate\Support\Facades\Route;

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
})->middleware(['auth', 'role:admin']);

Route::get('/attendance', function () {
    return 'user dashboard';
})->middleware(['auth', 'role:user']);