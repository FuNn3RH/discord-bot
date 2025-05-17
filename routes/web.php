<?php

use Illuminate\Support\Facades\Route;

// Route::controller(BoostController::class)->group(function () {
//     Route::get('/', 'index');
// });

Route::get('/', function () {

    test();
});

function test() {

    $runPot = (int) 4250;
    $runUnit = 't';

    // boosters
    $boostersCount = 3;

    $runPrice = $runPot / $boostersCount;
    $runPrice = number_format($runPrice, 2, '.', '');

    $runPrice = $runPrice * 2;
    dd($runPrice);
}
