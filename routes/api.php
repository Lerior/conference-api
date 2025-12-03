<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\TopicController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

//PUBLIC ROUTES
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('conference', [ConferenceController::class,'getConferences']);
Route::get('/conference/{id}',[ConferenceController::class,'getConferenceById']);
Route::get('/conference/{id}/topics',[ConferenceController::class,'getTopicsConference']);
Route::get('/conference/{id}/full',[ConferenceController::class,'getConferenceFull']);

Route::get('/topic/{id}',[TopicController::class,'getTopicById']);

//PRIVATE ROUTES
Route::middleware([IsUserAuth::class])->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        
        Route::post('logout', 'logout');
        Route::get('me', 'getUser');
    
    });

    Route::controller(ConferenceController::class)->group(function () {

        Route::post('conference', 'addConference');
        Route::patch('/conference/{id}', 'updateConferenceById');
        Route::delete('/conference/{id}', 'deleteConferenceById');

    });

    Route::controller(TopicController::class)->group( function () {

        Route::post('topic', 'addTopic');
        Route::patch('/topic/{id}', 'updateTopicById');
        Route::delete('/topic/{id}', 'deleteTopicById');

    });

    Route::controller(AttendanceController::class)->group( function () {

        Route::post('attendance', 'addAttendance');
        Route::get('attendance/me','getMyAttendances');
        Route::get('/attendance/{id}/conference','getConferenceAttendancesById');
        Route::delete('/attendance/{id}', 'deleteAttendanceByid');

    });

    //Principal logica sobre editar que solo se le permite al admin
    Route::middleware(IsAdmin::class)->group(function () {

        Route::get('topic', [TopicController::class,'getTopics']);
        Route::get('attendance',[AttendanceController::class,'getAttendances']);

    });
});