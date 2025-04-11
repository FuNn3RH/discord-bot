<?php

use App\Http\Controllers\BoostController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $username = 'extro999';

    Log::info($username);
    $userNicknames = User::pluck('nicknames', 'username')->toArray();

    $rows = DB::table('runs')
        ->where('deleted_at', null);

    $nicknames = array_filter($userNicknames, function ($nickname) use ($username) {
        return $nickname === $username;
    }, ARRAY_FILTER_USE_KEY);

    dd($nicknames);

    if (empty($nicknames)) {
        dd("You dont have account balance!");
        return false;
    }

    foreach ($nicknames as $nickname => $nicknames) {
        $rows->where(function ($query) use ($nicknames) {
            foreach ($nicknames as $nickname) {
                $query->orWhereJsonContains('boosters', $nickname);
            }
        });
    }

});
