<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MergeController;

Route::get('/', [MergeController::class, 'index']);
