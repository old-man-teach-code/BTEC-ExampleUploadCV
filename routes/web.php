<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CVController;

Route::get('/', [CVController::class, 'showForm'])->name('cv.form');
Route::post('/', [CVController::class, 'upload'])->name('cv.upload');
Route::post('/get-student-name', [CVController::class, 'getStudentName'])->name('cv.getStudentName');
