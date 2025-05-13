<?php

use App\Models\Run;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// Route::controller(BoostController::class)->group(function () {
//     Route::get('/', 'index');
// });

Route::get('/', function () {
    test();
});

function test() {

    // $runs = Run::withTrashed()->cursor();

    // $jsonData = [];
    // foreach ($runs as $run) {
    //     $jsonData[] = $run->toArray();
    // }

    // $jsonOutput = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // $backupPath = 'runs_discord-bot.json';
    // file_put_contents($backupPath, $jsonOutput);

    $filePath = public_path('runs_discord-bot.json');

    $runsData = File::get($filePath);

    $jsonData = json_decode($runsData, true);

    foreach ($jsonData as $run) {
        Run::create($run);
    }

    // unlink($filePath);
}
