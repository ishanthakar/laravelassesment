<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function () {
    echo 'Welcome to laravel assesment! Please use signup api to get registered!';
})->name('welcome');
Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::post('/login', 'LoginController@authenticate')->name("login");
    Route::post('/sign-up', 'LoginController@signUp')->name("signup");
    Route::post('/verify-account', 'LoginController@verifyAccount')->name("verifyAccount");
    
    Route::group(['middleware' => ['authenticateUser']], function () {
        Route::post('/change-password', 'LoginController@changePassword');
        Route::get('/me', 'LoginController@me');
        Route::post('/me', 'LoginController@updateProfile');
        Route::group(['middleware' => ['authenticateUser']], function () {
            Route::post('/invite-user', 'LoginController@inviteUser');
        });
    });
});
