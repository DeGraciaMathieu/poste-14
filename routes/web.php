<?php

use App\Http\Controllers\PosteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PosteController::class, 'index']);
Route::get('/indice', [PosteController::class, 'hint']);
