<?php

use App\Http\Controllers\BoostController;
use App\Models\Run;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $runs = Run::where('paid', 1)->get();
    foreach ($runs as $run) {
        dd($run, $run->payUser->duser_id);

    }
});
