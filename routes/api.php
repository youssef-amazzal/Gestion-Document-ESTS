<?php

use App\Http\Controllers\v1\FileApiController;
use App\Http\Controllers\v1\UserController;
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
//Route::prefix('v1')->group(function () {
//    Route::middleware('auth:sanctum')->get      ('files'        , [FileApiController::class, 'index' ]  );
//    Route::get      ('files/{file}' , [FileApiController::class, 'show'  ]  );
//    Route::post     ('files'        , [FileApiController::class, 'store' ]  );
//    Route::put      ('files/{file}' , [FileApiController::class, 'update']  );
//    Route::delete   ('files/{file}' , [FileApiController::class, 'delete']  );
//});

Route::apiResource('files', FileApiController::class);
Route::apiResource('users', UserApiController::class);

Route::get('login', function () {
    return '<h1>You are not logged in!</h1>';
})->name('login');
