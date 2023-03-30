<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\FileApiController;
use App\Http\Controllers\v1\FolderApiController;
use App\Http\Controllers\v1\GroupApiController;
use App\Http\Controllers\v1\OperationApiController;
use App\Http\Controllers\v1\PrivilegeApiController;
use App\Http\Controllers\v1\TagApiController;
use App\Http\Controllers\v1\UserApiController;
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

// version 1
Route::prefix('v1')->group(function () {

    // these routes don't require authentication
    Route::post('login', [AuthController::class, 'login']);

    // these routes require authentication
    Route::prefix('auth:sanctum')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);

        Route::middleware('role.admin')->group(function () {
            Route::prefix('users')->group(function () {
                Route::get('/', [UserApiController::class, 'index']);
                Route::post('/', [UserApiController::class, 'store']);
                Route::post('/{user}', [UserApiController::class, 'update']);
                Route::delete('/{user}', [UserApiController::class, 'destroy']);
            });
        });

        Route::prefix('files')->group(function () {
            Route::get('/', [FileApiController::class, 'index']);
            Route::post('/upload', [FileApiController::class, 'upload']);
            Route::get('/{file}', [FileApiController::class, 'show']);
            Route::get('/{file}/download', [FileApiController::class, 'download']);
            Route::put('/{file}', [FileApiController::class, 'update']);
            Route::delete('/{file}', [FileApiController::class, 'destroy']);
        });

        Route::apiResources([
            'folders'       => FolderApiController::class,
            'tags'          => TagApiController::class,
            'privileges'    => PrivilegeApiController::class,
            'operations'    => OperationApiController::class,
            'groups'        => GroupApiController::class,
        ]);

    });
});
