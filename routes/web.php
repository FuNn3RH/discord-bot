<?php

use App\Http\Controllers\BoostController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::controller(BoostController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('test', function () {
    $database = env("DB_DATABASE");

    $backupSql = "-- Database Backup: $database\n-- Created: " . date('Y-m-d H:i:s') . "\n\n";

    $tableName = 'runs';

    $rows = DB::table($tableName)->get();
    if (count($rows) > 0) {
        $backupSql .= "-- Dumping data for `$tableName`\n";
        foreach ($rows as $row) {
            $values = array_map(fn($value) => $value === null ? "NULL" : "'" . addslashes($value) . "'", (array) $row);
            $backupSql .= "INSERT INTO `$tableName` VALUES (" . implode(", ", $values) . ");\n";
        }
        $backupSql .= "\n";
    }

    $backupPath = storage_path('runs_discord-bot.sql');
    file_put_contents($backupPath, $backupSql);

    $data = ['sql' => $backupSql];
    $request = Http::post('https://myhome360.ir/mydocs/apitest/upload.php', $data);

    $msg = 'Backup Failed!';
    if ($request->successful()) {
        $msg = 'Backup Done!';
    }

    dd($msg);
});
