<?php

use App\Http\Controllers\BoostController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $file = database_path('backup.sql');

    $sql = File::get($file);
    DB::unprepared($sql);
});
