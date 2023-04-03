<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\FileApiController;
use App\Http\Controllers\v1\FolderApiController;
use App\Http\Controllers\v1\GroupApiController;
use App\Http\Controllers\v1\OperationApiController;
use App\Http\Controllers\v1\PrivilegeApiController;
use App\Http\Controllers\v1\SpaceApiController;
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
    // these routes do not require authentication
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });


    // these routes require authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'user']);
        });

        Route::middleware('role.admin')->group(function () {
            Route::apiResources([
                'users' => UserApiController::class,
            ]);
        });

        Route::prefix('files')->group(function () {
            Route::post('/upload', [FileApiController::class, 'upload']);
            Route::get('/{file}/download', [FileApiController::class, 'download']);
            Route::get('/shared', [FileApiController::class, 'getSharedWithMe']);
        });

        Route::prefix('spaces')->group(function () {
            Route::get('/personal', [SpaceApiController::class, 'getPersonalSpaces']);
            Route::get('/{space}/content', [SpaceApiController::class, 'getContent']);
        });

        Route::prefix('folders')->group(function () {
            Route::get('/{folder}/content', [FolderApiController::class, 'getContent']);
        });

        Route::apiResources([
            'files'         => FileApiController::class,
        ], ['except' => ['index', 'show', 'store']]);

        Route::apiResources([
            'folders'       => FolderApiController::class,
            'tags'          => TagApiController::class,
            'privileges'    => PrivilegeApiController::class,
            'operations'    => OperationApiController::class,
            'groups'        => GroupApiController::class,
        ], ['except' => ['index']]);

    });
});

