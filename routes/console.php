<?php

use App\Http\Controllers\DiscordBotController;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('discord:run', function () {
    /** @var ClosureCommand $this */
    $this->comment('Running the Discord bot');
    $discordBotController = new DiscordBotController();
    $discordBotController->startBot();

})->purpose('Runs the Discord bot');

Artisan::command('backup:old', function () {
    $file = database_path('backup.sql');

    $sql = File::get($file);
    DB::unprepared($sql);
});
