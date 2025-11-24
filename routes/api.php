<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConferenceController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//PUBLIC ROUTES
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('conferences', [ConferenceController::class,'getConferences']);

//PRIVATE ROUTES
Route::middleware([IsUserAuth::class])->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        
        Route::post('logout', 'logout');
        Route::get('me', 'getUser');
    
    });

    Route::controller(Conference::class)->group(function () {

        Route::post('conferences', 'addConference');
        Route::get('/conferences/{id}', 'getConferenceById');
        Route::patch('/conferences/{id}', 'updateConferenceById');
        Route::delete('/conferences/{id}', 'deleteConferenceById');

    });

    Route::middleware(IsAdmin::class)->group(function () {
        
        //Principal logica sobre editar que solo se le permite al admin

    });
});