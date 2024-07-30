<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\MailSendController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/credit', [CreditController::class, 'store']);

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);

Route::post('/datab', [CreditController::class, 'datab']);

Route::post('/userinfo', [UserController::class, 'userinfo']);

Route::post('/changeId', [UserController::class, 'changeId']);

Route::post('/changePw', [UserController::class, 'changePw']);

Route::post('/remove', [UserController::class, 'remove']);

Route::post('/mailSubmit', [MailSendController::class, 'mailSubmit']);

Route::post('/searchL', [SearchController::class, 'searchL']);

Route::post('/searchP', [SearchController::class, 'searchP']);