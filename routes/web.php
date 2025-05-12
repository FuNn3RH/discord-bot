<?php

use Illuminate\Support\Facades\Route;

// Route::controller(BoostController::class)->group(function () {
//     Route::get('/', 'index');
// });

Route::get('/', function () {
    test();
});

function test() {
    dd('h2');

}
