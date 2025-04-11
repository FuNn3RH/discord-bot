<?php

use App\Http\Controllers\BoostController;
use App\Models\Run;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {

    $username = '_funn3r';

    $nicknames = getNicknames();

    $runData = Run::find(98);

    $boosters = $runData->boosters;
    $boostersPayment = [];
    foreach ($nicknames as $username => $nicknameArray) {

        if (!$nicknameArray) {
            continue;
        }

        foreach ($boosters as $booster) {

            if (in_array($booster, $nicknameArray)) {
                if (isset($boostersPayment[$username])) {
                    $boostersPayment[$username] += 1;
                } else {
                    $boostersPayment[$username] = 1;
                }
            }
        }

    }

    dd($boostersPayment);
});

function getNicknames() {

    if (!$nicknames = Cache::get('nicknames')) {
        $nicknames = User::pluck('nicknames', 'username')->toArray();
        Cache::put('nicknames', $nicknames, now()->addDay());
    }

    return $nicknames;
}
