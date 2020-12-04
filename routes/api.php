<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => 'userauth:api'], function () {
    Route::get('/', function () {
        return response()->json(['Welcome' => 'Api starts'], 200);
    });
    Route::post('/register', 'Api\AuthController@userCreate');
    Route::post('/login', 'Api\AuthController@userLogin');
    Route::get('logout', 'Api\AuthController@userLogout');

});

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('/student', 'Api\StudentController');
    Route::apiResource('/lecturer', 'Api\LecturerController');
    Route::apiResource('/faculty', 'Api\FacultyController');
    Route::apiResource('/department', 'Api\DepartmentController');
    Route::apiResource('/course', 'Api\CourseController');
    Route::apiResource('/attendance', 'Api\AttendanceController');
});

Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});

