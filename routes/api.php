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

/*
 * Public Routes
 * */

Route::get('posts', [\App\Http\Controllers\PostController::class, 'index']);
// for visibility
Route::post('get-posts', [\App\Http\Controllers\PostController::class, 'index']);

// Get Post
Route::get('posts/{id}', [\App\Http\Controllers\PostController::class, 'show']);

// Get all Authors
Route::get('authors', [\App\Http\Controllers\UserController::class, 'index']);
// Show Author Details
Route::get('authors/{id}', [\App\Http\Controllers\UserController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('posts', [\App\Http\Controllers\PostController::class, 'store']);
    Route::get('queryNewPosts', [\App\Http\Controllers\PostController::class, 'queryNewPostsFromThirdParty']);
});
