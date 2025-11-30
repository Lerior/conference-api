<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\TopicController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use App\Models\Conference;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//PUBLIC ROUTES
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('conferences', [ConferenceController::class,'getConferences']);
Route::get('/conferences/{id}',[ConferenceController::class,'getConferenceById']);
Route::get('conferencetopics',[ConferenceController::class,'getTopicsConference']);
Route::get('/topics/{id}',[TopicController::class,'getTopicById']);

//PRIVATE ROUTES
Route::middleware([IsUserAuth::class])->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        
        Route::post('logout', 'logout');
        Route::get('me', 'getUser');
    
    });

    Route::controller(Conference::class)->group(function () {

        Route::post('conferences', 'addConference');
        Route::patch('/conferences/{id}', 'updateConferenceById');
        Route::delete('/conferences/{id}', 'deleteConferenceById');

    });

    Route::controller(Topic::class)->group( function () {

        Route::post('topics', 'addTopic');
        Route::patch('/topics/{id}', 'updateTopicById');
        Route::delete('/topics/{id}', 'deteTopicById');

    });

    Route::controller(AttendanceController::class)->group( function () {

        Route::post('attendances', 'addAttendance');
        Route::get('myattendances','getMyAttendances');
        Route::get('/attendancesconference/{id}','getConferenceAttendancesById');

    });

    Route::middleware(IsAdmin::class)->group(function () {
        
        //Principal logica sobre editar que solo se le permite al admin

        Route::get('topics', [TopicController::class,'getTopics']);
        Route::get('attendances',[AttendanceController::class,'getAttendances']);

    });
});