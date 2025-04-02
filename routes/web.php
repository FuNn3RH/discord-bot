<?php

use App\Http\Controllers\BoostController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $yesterdayDate = Carbon::now()->subDay(1)->format('Y-m-d');
    $currentDate = Carbon::now()->format('Y-m-d');

    $startTime = $yesterdayDate . " 07:00:00";
    $endTime = $currentDate . " 06:59:59";

    $username = 'funn3r';

    $nicknames = [
        'kallagh' => ['mmdraven', 'raven', 'mamadraven', 'kallagh'],
        'funn3r' => ['funn3r', 'funner'],
        'amirparse' => ['amirparse', 'parse'],
    ];

    $isToday = true;

    $rows = DB::table('runs')
        ->where('deleted_at', null);

    $nicknames = array_filter($nicknames, function ($nickname) use ($username) {
        return $nickname === $username;
    }, ARRAY_FILTER_USE_KEY);

    foreach ($nicknames as $nickname => $nicknames) {
        $rows->where(function ($query) use ($nicknames) {
            foreach ($nicknames as $nickname) {
                $query->orWhereJsonContains('boosters', $nickname);
            }
        });
    }

    if ($isToday) {
        $rows->whereBetween('created_at', [$startTime, $endTime]);
    }

    $rows = $rows->get();

    foreach ($rows as $row) {
        $cutCount = collect(json_decode($row->boosters))->countBy()->get($nickname, 0);

        if ($cutCount > 1) {
            for ($i = 0; $i < $cutCount - 1; $i++) {
                $rows->push($row);
            }
        }
    }

    $pendingRuns = $rows->where('paid', 0)->unique('id')->pluck('id')->join(',');

    $totalRuns = $rows->sum('count');

    $paidBalanceT = $rows->where('paid', 1)
        ->whereIn('unit', ['T', 't'])
        ->sum('price');

    $paidBalanceK = $rows->where('paid', 1)
        ->whereIn('unit', ['K', 'k'])
        ->sum('price');

    $totalBalanceT = $rows->whereIn('unit', ['T', 't'])
        ->sum('price');

    $totalBalanceK = $rows->whereIn('unit', ['K', 'k'])
        ->sum('price');

    $pendingBalanceT = $totalBalanceT - $paidBalanceT;
    $pendingBalanceK = $totalBalanceK - $paidBalanceK;

    $text = '';
    if ($isToday) {
        $text .= "**Today Balance**\n";
    }

    $text .= "Your balance is: \n\n" .
        "**Pending**: \n" .
        $pendingBalanceT . " **T**\n" .
        $pendingBalanceK . " **K**\n";

    if ($pendingRuns) {
        $text .= "**Runs id**: [" . $pendingRuns . "]\n";
    }

    $text .= "\n**Paid**: \n" .
        $paidBalanceT . " **T**\n" .
        $paidBalanceK . " **K**\n\n" .
        "**Total**: \n" .
        $totalBalanceT . " **T**\n" .
        $totalBalanceK . " **K**\n\n" .
        "**Runs Count**:" . $totalRuns;

    dd($text);
});
