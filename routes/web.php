<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [UserController::class, 'index'], function () {
    return view('users');
});
Route::post('/', [UserController::class, 'create']);
Route::get('/{id}', [UserController::class, 'get']);
Route::put('/{id}', [UserController::class, 'update']);
Route::delete('/{id}', [UserController::class, 'delete']);
