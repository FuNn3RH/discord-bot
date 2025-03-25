<?php

use App\Http\Controllers\DiscordBotController;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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
