<?php

use App\Http\Controllers\BoostController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $now = Carbon::today();

    if ($now->hour < 7) {
        $startTime = Carbon::yesterday()->setHour(7)->setMinute(0)->setSecond(0);
        $endTime = Carbon::today()->setHour(6)->setMinute(59)->setSecond(59);
    } else {
        $startTime = Carbon::today()->setHour(7)->setMinute(0)->setSecond(0);
        $endTime = Carbon::tomorrow()->setHour(6)->setMinute(59)->setSecond(59);
    }

    $startTime = $startTime->format('Y-m-d H:i:s');
    $endTime = $endTime->format('Y-m-d H:i:s');

    dd($startTime, $endTime);
});
