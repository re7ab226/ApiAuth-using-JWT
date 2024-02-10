<?php

use App\Http\Controllers\API\UserController;
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
Route::get('verifymail/{token}',[UserController::class,'VerifyEmail']) ;
Route::get('reset-password',[UserController::class,'ResetPasswordLoad']) ;
Route::post('reset-password',[UserController::class,'ResetPassword']) ;




