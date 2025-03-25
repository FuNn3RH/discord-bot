<?php

use App\Http\Controllers\BoostController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $authUser = User::where('duser_id', '358758841850265610')->first();
    dd($authUser);
});
