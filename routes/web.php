<?php

use App\Http\Controllers\BoostController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {

    $nicknames = [
        'kallagh' => ['mmdraven', 'raven', 'mamadraven', 'kallagh'],
        'funn3r' => ['funn3r', 'funner'],
        'amirparse' => ['amirparse', 'parse'],
    ];

    $isToday = true;

    $username = 'mmdraven';

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
        $startTime = Carbon::today()->setHour(7)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addDay()->subSecond();

        $startTime = $startTime->format('Y-m-d H:i:s');
        $endTime = $endTime->format('Y-m-d H:i:s');
        $rows->whereBetween('created_at', [$startTime, $endTime]);
    }

    $rows = $rows->get();

    foreach ($rows as $row) {
        $boostersNames = json_decode($row->boosters);
        $cutCount = 0;
        foreach ($boostersNames as $boostersName) {
            if (in_array($boostersName, $nicknames)) {
                $cutCount++;
            }
        }

        if ($cutCount > 1) {
            for ($i = 0; $i < $cutCount - 1; $i++) {
                $rows->push($row);
            }
        }
    }

    dd($rows->pluck('boosters'));
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
