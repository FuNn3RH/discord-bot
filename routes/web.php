<?php

use App\Http\Controllers\BoostController;
use App\Models\Run;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $runs = Run::all();

    $jsonPath = 'runs.json';
    file_put_contents($jsonPath, $runs);

    $filePath = public_path('runs.json');
    $file = File::get($filePath);

    File::delete($filePath);
});
