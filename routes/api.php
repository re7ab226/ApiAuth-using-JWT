<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();

// });
Route::post('forget-password',[UserController::class,'ForgetPassword']);

Route::group(['Middelware' =>'api'],function($routes){
    Route::post('register',[UserController::class,'register']);
    Route::get('send-verify-mail/{email}',[UserController::class,'sendVerifyMail']);
    Route::post('login',[UserController::class,'login']);
    Route::get('profile',[UserController::class,'profile']);
    Route::post('profile-update',[UserController::class,'profileUpdate']);
    Route::get('refresh',[UserController::class,'refresh']);


    Route::get('logout',[UserController::class,'logout']);


});
