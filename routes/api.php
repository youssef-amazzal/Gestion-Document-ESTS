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


        // don't know why, but this route can't be found if it's under apiResources
        Route::prefix('users')->group(function () {
            Route::get('students', [UserApiController::class, 'getStudents']);
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
            Route::put('/{file}/share', [FileApiController::class, 'share']);
            Route::get('/members', [FileApiController::class, 'getPotentialMembers']);
            Route::put('/{file}/move', [FileApiController::class, 'move']);
            Route::put('/{file}/rename', [FileApiController::class, 'rename']);
            Route::post('/{file}/shortcut', [FileApiController::class, 'createShortcut']);
            Route::put('/{file}/pin', [FileApiController::class, 'togglePin']);
        });

        Route::prefix('spaces')->group(function () {
            Route::get('/personal', [SpaceApiController::class, 'getPersonalSpaces']);
            Route::get('/{space}/content', [SpaceApiController::class, 'getContent']);
            Route::get('/shared', [SpaceApiController::class, 'getSharedWithMe']);
            Route::put('/{space}/share', [SpaceApiController::class, 'share']);
            Route::get('/members', [SpaceApiController::class, 'getPotentialMembers']);
            Route::get('/tree', [SpaceApiController::class, 'getTree']);
        });

        Route::prefix('folders')->group(function () {
            Route::get('/{folder}/content', [FolderApiController::class, 'getContent']);
            Route::put('/{folder}/share', [FolderApiController::class, 'share']);
            Route::get('/shared', [FolderApiController::class, 'getSharedWithMe']);
            Route::get('/members', [FolderApiController::class, 'getPotentialMembers']);
            Route::put('/{folder}/move', [FolderApiController::class, 'move']);
            Route::put('/{folder}/rename', [FolderApiController::class, 'rename']);
            Route::post('/{folder}/shortcut', [FolderApiController::class, 'createShortcut']);
            Route::put('/{folder}/pin', [FolderApiController::class, 'togglePin']);
        });

        Route::prefix('groups')->group(function () {
            Route::get('/owned', [GroupApiController::class, 'getOwnedGroups']);
            Route::get('/members', [GroupApiController::class, 'getPotentialMembers']);
            Route::put('/{group}/toggle', [GroupApiController::class, 'toggleMembers']);
            Route::put('/{group}/rename', [GroupApiController::class, 'rename']);
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
            'spaces'        => SpaceApiController::class,
        ], ['except' => ['index']]);

    });
});

