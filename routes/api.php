<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmotionController;

Route::post('/emotion-records', [EmotionController::class, 'store'])
    ->name('emotion-records');
