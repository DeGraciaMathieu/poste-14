<?php

use App\Http\Controllers\PosteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PosteController::class, 'index']);
Route::get('/interception/{interception}', [PosteController::class, 'show'])->whereNumber('interception');
Route::get('/interception/{interception}/indice', [PosteController::class, 'hint'])->whereNumber('interception');
Route::get('/interception/{interception}/origine', [PosteController::class, 'origine'])->whereNumber('interception');
