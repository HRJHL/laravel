<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\MailSendController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/credit', [CreditController::class, 'store']);

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);

Route::post('/datab', [CreditController::class, 'datab']);

Route::post('/mailSubmit', [MailSendController::class, 'mailSubmit']);