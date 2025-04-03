<?php

use App\Http\Controllers\BoostController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $startTime = Carbon::today()->setHour(7)->setMinute(0)->setSecond(0);
    $endTime = $startTime->copy()->addDay()->subSecond();

    $startTime = $startTime->format('Y-m-d H:i:s');
    $endTime = $endTime->format('Y-m-d H:i:s');

});
