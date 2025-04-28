<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', [LoginController::class, 'loginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('employees/{id}/download-qr', [EmployeeController::class, 'downloadQrCode'])->name('employees.downloadQrCode');
Route::resource('employees', EmployeeController::class);