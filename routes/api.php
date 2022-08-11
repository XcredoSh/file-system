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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api) {
    $api->post('login', [\App\Http\Controllers\Api\AuthController::class, 'userLogin']);

    $api->group(['middleware' => ['jwt.verify']], function($api){
        $api->get('me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        $api->post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    });

    $api->group(['middleware' => ['role:super-admin', 'jwt.verify'], 'prefix' => 'admin'], function($api){
        $api->get('users', [\App\Http\Controllers\Api\Admin\AdminUsersController::class, 'index']);
        $api->post('users/register', [\App\Http\Controllers\Api\Admin\AdminUsersController::class, 'store']);
    });

    $api->group(['middleware' => ['role:simple-user', 'jwt.verify'], 'prefix' => 'user'], function($api){
        $api->get('files', [\App\Http\Controllers\Api\User\UserFileController::class, 'index']);
        $api->post('upload', [\App\Http\Controllers\Api\User\UserFileController::class, 'store']);
    });

    $api->group(['middleware' => ['role:moderator', 'jwt.verify'], 'prefix' => 'moderator'], function($api){
        $api->get('me', [\App\Http\Controllers\Api\Moderator\ModeratorController::class, 'me']);
        $api->get('files', [\App\Http\Controllers\Api\Moderator\ModeratorFileController::class, 'index']);
        $api->get('file/{id}', [\App\Http\Controllers\Api\Moderator\ModeratorFileController::class, 'show']);
        $api->delete('file/{id}', [\App\Http\Controllers\Api\Moderator\ModeratorFileController::class, 'destroy']);
    });
});


