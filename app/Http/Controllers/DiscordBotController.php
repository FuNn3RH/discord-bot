<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Run;
use App\Models\User;
use Carbon\Carbon;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiscordBotController extends Controller {
    protected $discord;
    protected $authUser;
    protected $message;

    public function __construct() {
        $this->discord = new Discord([
            'token' => env('DISCORD_BOT_TOKEN'),
            'intents' => Intents::GUILDS | Intents::GUILD_MESSAGES | Intents::MESSAGE_CONTENT | Intents::DIRECT_MESSAGES | Intents::GUILD_MESSAGE_REACTIONS,
        ]);
    }

    public function startBot() {

        $this->discord->on('init', function (Discord $discord) {
            Log::info('Bot is ready!');

            $addRunCmd = new Command($discord, [
                'name' => 'arun',
                'description' => 'Adds Run Attendance',
                'options' => [
                    [
                        'name' => 'advertiser',
                        'description' => 'Name of the Advertiser',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'dungeons',
                        'description' => 'List of the dungeons sperated with - ex:FG,FG-ML',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'run_count',
                        'description' => 'Count of the runs',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'run_level',
                        'description' => 'Level of the runs',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'run_pot',
                        'description' => 'Total Pot of the run',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'unit',
                        'description' => 'Unit of the Pot (K/T)',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'boosters_name',
                        'description' => 'Boosters name list seperated with -',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'additional_note',
                        'description' => 'Additional note for the run',
                        'type' => Option::STRING,
                        'required' => false,
                    ],
                ],
            ]);

            $editRunCmd = new Command($discord, [
                'name' => 'erun',
                'description' => 'Edits Run Attendance',
                'options' => [
                    [
                        'name' => 'run_id',
                        'description' => 'ID of the run',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'advertiser',
                        'description' => 'Name of the Advertiser',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'dungeons',
                        'description' => 'List of the dungeons sperated with - ex:FG,FG-ML',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'run_count',
                        'description' => 'Count of the runs',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'run_level',
                        'description' => 'Level of the runs',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'run_pot',
                        'description' => 'Total Pot of the run',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'unit',
                        'description' => 'Unit of the Pot (K/T)',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'boosters_name',
                        'description' => 'Boosters name list seperated with -',
                        'type' => Option::STRING,
                        'required' => true,
                    ],
                    [
                        'name' => 'additional_note',
                        'description' => 'Additional note for the run',
                        'type' => Option::STRING,
                        'required' => false,
                    ],
                ],
            ]);

            // $removeRunCmd = new Command($discord, [
            //     'name' => 'rrun',
            //     'description' => 'Removes Run Attendance',
            //     'options' => [
            //         [
            //             'name' => 'run_id',
            //             'description' => 'ID of the run',
            //             'type' => Option::STRING,
            //             'required' => true,
            //         ],
            //     ],
            // ]);

            // $showRunCmd = new Command($discord, [
            //     'name' => 'srun',
            //     'description' => 'Shows Run Attendance',
            //     'options' => [
            //         [
            //             'name' => 'run_id',
            //             'description' => 'ID of the run',
            //             'type' => Option::STRING,
            //             'required' => true,
            //         ],
            //     ],
            // ]);

            // Register command
            $discord->application->commands->save($addRunCmd); # Add
            $discord->application->commands->save($editRunCmd); # Edit
            // $discord->application->commands->save($removeRunCmd); # Delete
            // $discord->application->commands->save($showRunCmd); # Show

        });

        $this->discord->on(Event::MESSAGE_CREATE, function ($message, Discord $discord) {

            if ($this->checkUser($message)) {
                $this->message = $message;
                $this->checkCommands($message);
            }

        });

        $this->discord->on(Event::MESSAGE_REACTION_ADD, function ($reaction, Discord $discord) {
            if (!$reaction?->member?->user->bot) {
                $run = $this->findRun($reaction);
                $this->handleReaction($reaction, $run);
            }
        });

        $this->discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
            $messageBuilder = MessageBuilder::new ();
            if (!$this->checkUser($interaction, true)) {
                $interaction->respondWithMessage($messageBuilder->setContent('You do not have permission to use this command'), true);
                return;
            }

            switch ($interaction->data->name) {
            case 'arun':
                $this->addRunWithCommand($interaction);
                break;
            case 'erun':
                $this->editRunWithCommand($interaction);
                break;
            default:
                break;
            }
        });

        $this->discord->run();
    }

    protected function checkUser($message, $isCommand = false) {
        $author = $isCommand ? $message->user->id : $message->author->id;

        $this->authUser = User::where('duser_id', $author)->first();
        return $this->authUser !== null; // Cleaner boolean check
    }

    protected function checkCommands($message) {
        $command = $message->content;

        $messageContent = explode(' ', $message->content);

        if (count($messageContent) > 1) {
            $command = $messageContent[0];
        }

        match ($command) {
            '!aadmin' => $this->addAdmin($message),
            '!radmin' => $this->removeAdmin($message),
            '!arun' => $this->addRun($message),
            '!rrun' => $this->removeRun($message),
            '!erun' => $this->editRun($message),
            '!srun' => $this->showRun($message),
            '!!b' => $this->showBalance($message),
            '!unpaids' => $this->myUnPaids($message),
            '!myadds' => $this->myAdds($message),
            '!!bt' => $this->showBalance($message, true),
            default => null,
        };
    }

    protected function addAdmin($message) {

        $messageContent = $message->content;

        $pattern = '/!aadmin\s\w+\s\w+\s\w+/';

        if (!preg_match($pattern, $messageContent)) {
            $message->reply('Invalid command format !aadmin <username> <userid> <name>');
            return;
        }

        $messageContent = explode(' ', $messageContent);
        unset($messageContent[0]);
        $messageContent = array_filter($messageContent);
        $messageContent = array_values($messageContent);

        $newUserData['username'] = $messageContent[0];
        $newUserData['duser_id'] = $messageContent[1];
        $newUserData['name'] = $messageContent[2];

        $validator = Validator::make($newUserData, [
            'username' => ['required', 'unique:users,username'],
            'duser_id' => ['required', 'unique:users,duser_id'],
            'name' => ['required'],
        ]);

        if ($validator->fails()) {
            $message->reply($validator->errors()->first());
            return;
        }

        $newUser = User::create($validator->valid());

        if ($newUser) {
            $message->reply('User added successfully');
        } else {
            $message->reply('User not added');
        }
    }

    protected function removeAdmin($message) {

        if ($this->authUser->username != 'funn3r') {
            return;
        }

        $messageContent = $message->content;

        $pattern = '/!radmin\s\w+/';

        if (!preg_match($pattern, $messageContent)) {
            $message->reply('Invalid command format !radmin <userid>');
            return;
        }

        $messageContent = explode(' ', $message->content);
        unset($messageContent[0]);
        $userid = $messageContent[1];

        $validator = Validator::make(['userid' => $userid], [
            'userid' => ['required'],
        ]);

        if ($validator->fails()) {
            $message->reply($validator->errors()->first());
            return;
        }

        $user = User::where('duser_id', $userid)->first();

        if ($user) {
            $user->delete();
            $message->reply('User removed successfully');
        } else {
            $message->reply('User not removed');
        }
    }

    protected function handleAddRunCommand($message) {
        $messageContent = $message->content;

        $messageContent = trim($messageContent);

        $pattern = '/^!arun .+ \d+[xX]\d+ [A-Z0-9a-z\-]+ \d+[tTkK] -> (?:.+-)*\w+(?: .*)?$/';

        if (!preg_match($pattern, $messageContent)) {
            // $message->reply('Invalid command format !arun <adv> <dungeons> <count>x<pot> -> <boosters (ex:bob-alex-john-clark)> <?note>');
            $message->reply('Invalid command format !arun <adv> <dungeons> <count>x<level> <pot><t/k> -> <boosters (ex:bob-alex-john-clark)> <?note>');
            return false;
        }

        $messageContent = explode(' ', $messageContent);
        $messageContent = str_replace('->', '', $messageContent);
        $messageContent = array_filter($messageContent);

        unset($messageContent[0]); // remove the command

        $messageContent = implode(' ', $messageContent);
        $runData = explode(' ', $messageContent);

        // adv
        $runAdv = $runData[0];

        // count x level
        $runInfo = explode('x', strtolower($runData[1]));
        $runCount = $runInfo[0];
        $runLevel = $runInfo[1];

        // dungeons
        $dungeons = $runData[2];

        // price
        $runPot = (int) $runData[3];
        $runUnit = preg_replace('/[\d]/', '', $runData[3]);

        // boosters
        $runBoosters = explode('-', $runData[4]);
        $boostersCount = count($runBoosters);

        $runPrice = $runPot / $boostersCount;
        $runPrice = number_format($runPrice, 2);

        // note
        $runNote = '';
        if (count($runData) > 5) {
            $runNote = implode(' ', array_slice($runData, 5));
        }

        return [
            'runAdv' => $runAdv,
            'runCount' => $runCount,
            'runLevel' => $runLevel,
            'runPrice' => $runPrice,
            'dungeons' => $dungeons,
            'runPot' => $runPot,
            'runUnit' => $runUnit,
            'runNote' => $runNote,
            'runBoosters' => $runBoosters,
            'boostersCount' => $boostersCount,
        ];
    }

    protected function addRun($message) {
        DB::transaction(function () use ($message) {
            $runData = $this->handleAddRunCommand($message);
            if (!$runData) {
                return;
            }

            $runsChannel = Channel::where('channel_name', 'runs')->first();

            $run = Run::create([
                'count' => $runData['runCount'],
                'level' => $runData['runLevel'],
                'dungeons' => $runData['dungeons'],
                'boosters' => $runData['runBoosters'],
                'boosters_count' => $runData['boostersCount'],
                'price' => $runData['runPrice'],
                'unit' => $runData['runUnit'],
                'pot' => $runData['runPot'],
                'adv' => $runData['runAdv'],
                'note' => $runData['runNote'],
                'user_id' => $this->authUser->id,
                'channel_id' => $runsChannel?->id,
                'dmessage_id' => null,
                'dmessage_link' => null,
            ]);

            if ($run) {
                $message->reply('Run added successfully');
                $this->announceRuns($run, $runsChannel);
            } else {
                $message->reply('Run not added');
            }
        });
    }

    protected function removeRun($message) {
        DB::transaction(function () use ($message) {
            $messageContent = $message->content;

            $pattern = '/!rrun\s\w+/';

            if (!preg_match($pattern, $messageContent)) {
                $message->reply('Invalid command format !rrun <runid>');
                return;
            }

            $messageContent = explode(' ', $message->content);
            unset($messageContent[0]);
            $runId = $messageContent[1];

            $run = Run::find($runId);

            if ($run) {
                $channel = $this->discord->getChannel($run->channel->dchannel_id);
                $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($run) {

                    $text = $run->message;
                    $text .= "\n\n❌ **Run removed by " . $this->authUser->name . '** ❌';

                    $embed = new Embed($this->discord);
                    $embed->setTitle("**Butterfly Boost Attendance**")
                        ->setColor(0xdf3079)
                        ->setDescription($text)
                        ->setFooter("Attendance by " . $this->authUser->name)
                        ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

                    $messageBuilder = MessageBuilder::new ()
                        ->setContent('')
                        ->addEmbed($embed);

                    $discordMessage->edit($messageBuilder)->then(function ($message) {
                        $message->react('❌');
                    });

                }, function ($error) {
                    echo "Error fetching message: " . $error->getMessage();
                });
                $run->delete();
                $message->reply('Run removed successfully');
            } else {
                $message->reply('Run not removed');
            }
        });
    }

    protected function editRun($message) {
        DB::transaction(function () use ($message) {

            $messageContent = $message->content;

            $messageContent = trim($messageContent);

            $pattern = '/^!erun \d+ .+ \d+[xX]\d+ [A-Z0-9\-]+ \d+[tTkK] -> (?:.+-)*\w+(?: .*)?$/';

            if (!preg_match($pattern, $messageContent)) {
                $message->reply('Invalid command format !erun <runid> <adv> <dungeons> <count>x<pot> -> <boosters (ex:bob-alex-john-clark)> <?note>');
                return false;
            }

            $messageContent = explode(' ', $messageContent);
            $messageContent = str_replace('->', '', $messageContent);
            $messageContent = array_filter($messageContent);

            unset($messageContent[0]); // remove the command

            $runId = $messageContent[1];
            unset($messageContent[1]); // remove the runId

            $messageContent = implode(' ', $messageContent);
            $runData = explode(' ', $messageContent);

            // adv
            $runAdv = $runData[0];

            // count x level
            $runInfo = explode('x', strtolower($runData[1]));
            $runCount = $runInfo[0];
            $runLevel = $runInfo[1];

            // dungeons
            $dungeons = $runData[2];

            // price
            $runPot = (int) $runData[3];
            $runUnit = preg_replace('/[\d]/', '', $runData[3]);

            // boosters
            $runBoosters = explode('-', $runData[4]);
            $boostersCount = count($runBoosters);

            $runPrice = $runPot / $boostersCount;
            $runPrice = number_format($runPrice, 2);

            // note
            $runNote = '';
            if (count($runData) > 5) {
                $runNote = implode(' ', array_slice($runData, 5));
            }

            $run = Run::find($runId);

            if ($run) {
                $run->update([
                    'adv' => $runAdv,
                    'count' => $runCount,
                    'level' => $runLevel,
                    'price' => $runPrice,
                    'dungeons' => $dungeons,
                    'pot' => $runPot,
                    'unit' => $runUnit,
                    'note' => $runNote,
                    'boosters' => $runBoosters,
                    'boosters_count' => $boostersCount,
                    'message' => $this->runsText($run),
                ]);

                $run->refresh();

                $channel = $this->discord->getChannel($run->channel->dchannel_id);
                $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($run) {

                    $text = $run->message;
                    $text .= "\n**Edited by **" . $this->authUser->username;
                    $text .= "\n**Edited at**: " . $run->updated_at;

                    $color = 0x483868;
                    if ($run->paid) {
                        $color = 0x4caf50;
                        $text .= "\n\n✅ **Run paid by " . $run->pay_user ?? '-' . '** ✅';
                        $text .= "\n**Paid at**: " . $run->paid_at;

                    }

                    $embed = new Embed($this->discord);
                    $embed->setTitle("**Butterfly Boost Attendance**")
                        ->setColor($color)
                        ->setDescription($text)
                        ->setFooter("Attendance by " . $run->user->name)
                        ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

                    $messageBuilder = MessageBuilder::new ()
                        ->setContent('')
                        ->addEmbed($embed);

                    $discordMessage->edit($messageBuilder);
                });
            }
        });

    }

    protected function showRun($message) {
        $messageContent = $message->content;

        $messageContent = trim($messageContent);

        $pattern = '/^!srun \d+$/';

        if (!preg_match($pattern, $messageContent)) {
            $message->reply('Invalid command format !srun <runid>');
            return false;
        }

        $messageContent = explode(' ', $messageContent);
        $runId = $messageContent[1];

        $run = Run::find($runId);

        if ($run) {
            $text = $run->message;

            if ($run->paid == 1) {
                $text .= "\n\n✅ **Run paid by " . $run->pay_user . '** ✅';
                $text .= "\n**Paid at**: " . $run->paid_at;
            }

            $embed = new Embed($this->discord);
            $embed->setTitle("**Butterfly Boost Attendance**")
                ->setColor(0x483868)
                ->setDescription($text)
                ->setFooter("Attendance by " . $run->user->name)
                ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

            $messageBuilder = MessageBuilder::new ()
                ->setContent('')
                ->addEmbed($embed);
            $message->reply($messageBuilder);
        }
    }

    protected function addRunWithCommand($interaction) {
        // $interaction->respondWithMessage("Processing your add run request ", true);
        $interaction->acknowledge(true);

        DB::transaction(function () use ($interaction) {

            $options = $interaction->data->options;

            $boostersName = explode('-', $options['boosters_name']->value);
            $boostersCount = count($boostersName);

            $runPot = (int) $options['run_pot']->value;
            $runPrice = $runPot / $boostersCount;
            $runPrice = number_format($runPrice, 2);

            $runUnit = $options['unit']->value;

            $runsChannel = Channel::where('channel_name', 'runs')->first();
            if ($runsChannel) {
                $run = Run::create([
                    'count' => $options['run_count']->value,
                    'level' => $options['run_level']->value,
                    'dungeons' => $options['dungeons']->value,
                    'boosters' => $boostersName,
                    'boosters_count' => $boostersCount,
                    'price' => $runPrice,
                    'unit' => $runUnit ?? '?',
                    'pot' => $runPot,
                    'adv' => $options['advertiser']->value,
                    'note' => $options['additional_note']?->value,
                    'user_id' => $this->authUser->id,
                    'channel_id' => $runsChannel?->id,
                    'dmessage_id' => null,
                    'dmessage_link' => null,
                ]);

                if ($run) {
                    $run->refresh();

                    $this->announceRuns($run, $runsChannel);

                    $interaction->followUp("Run added successfully! {$run->dmessage_link}");
                } else {

                    $interaction->followUp("Failed to add the run. Please try again.");
                }
            } else {
                $interaction->followUp("No runs channel found. Please contact the admin.");
            }
        });

    }

    protected function editRunWithCommand($interaction) {
        $interaction->respondWithMessage("Processing your edit run request ", true);

        DB::transaction(function () use ($interaction) {
            $options = $interaction->data->options;

            $boostersName = explode('-', $options['boosters_name']->value);
            $boostersCount = count($boostersName);

            $runPot = (int) $options['run_pot']->value;
            $runPrice = $runPot / $boostersCount;
            $runPrice = number_format($runPrice, 2);

            $runUnit = $options['unit']->value;

            $runId = $options['run_id']->value;
            $runsChannel = Channel::where('channel_name', 'runs')->first();
            if ($runsChannel) {
                $run = Run::find($runId);

                if ($run) {
                    $run->update([
                        'count' => $options['run_count']->value,
                        'level' => $options['run_level']->value,
                        'dungeons' => $options['dungeons']->value,
                        'boosters' => $boostersName,
                        'boosters_count' => $boostersCount,
                        'price' => $runPrice,
                        'unit' => $runUnit ?? '?',
                        'pot' => $runPot,
                        'adv' => $options['advertiser']->value,
                        'note' => $options['additional_note']?->value,
                        'user_id' => $this->authUser->id,
                        'channel_id' => $runsChannel?->id,
                        'message' => $this->runsText($run),
                    ]);
                    $run->refresh();

                    $channel = $this->discord->getChannel($run->channel->dchannel_id);
                    $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($run, $interaction) {

                        $text = $run->message;
                        $text .= "\n**Edited by **" . $this->authUser?->username ?? '-';
                        $text .= "\n**Edited at**: " . $run->updated_at;

                        $color = 0x483868;
                        if ($run->paid) {
                            $color = 0x4caf50;
                            $text .= "\n\n✅ **Run paid by " . $run->pay_user ?? '-' . '** ✅';
                            $text .= "\n**Paid at**: " . $run->paid_at;

                        }

                        $embed = new Embed($this->discord);
                        $embed->setTitle("**Butterfly Boost Attendance**")
                            ->setColor($color)
                            ->setDescription($text)
                            ->setFooter("Attendance by " . $run->user->name)
                            ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

                        $messageBuilder = MessageBuilder::new ()
                            ->setContent('')
                            ->addEmbed($embed);

                        $discordMessage->edit($messageBuilder);

                        $interaction->followUp("Run edited successfully!" . $run->dmessage_link);
                    });

                }
            }
        }, 3);
    }

    protected function removeRunWithCommand($interaction) {
        $interaction->respondWithMessage("Processing your request...", true);
    }

    protected function showRunWithCommand($interaction) {
        $interaction->respondWithMessage("Processing your request...", true);
    }

    protected function changePaidRun($run, $reaction) {
        if ($run->paid == 1) {
            return;
        }

        $run->paid = 1;
        $run->paid_at = now();
        $run->pay_user = $reaction->member->username;
        $run->save();

        $payUser = $reaction->member;

        $channel = $this->discord->getChannel($run->channel->dchannel_id);
        $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($run, $payUser) {

            $text = $run->message;
            $text .= "\n\n✅ **Run paid by " . $payUser . '** ✅';
            $text .= "\n**Paid at**: " . $run->paid_at;

            $embed = new Embed($this->discord);
            $embed->setTitle("**Butterfly Boost Attendance**")
                ->setColor(0x4caf50)
                ->setDescription($text)
                ->setFooter("Attendance by " . $run->user->name)
                ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

            $messageBuilder = MessageBuilder::new ()
                ->setContent('')
                ->addEmbed($embed);

            $discordMessage->edit($messageBuilder)->then(function ($message) {
                $message->react('✅');
            });

        }, function ($error) {
            echo "Error fetching message: " . $error->getMessage();
        });

    }

    protected function announceRuns($runData, $runsChannel, $interaction = null) {

        if ($runsChannel) {

            $channel = $this->discord->getChannel($runsChannel->dchannel_id);
            if ($channel) {
                $promise = $channel->sendMessage('', false, $this->runsTemplate($runData));

                $promise->then(function ($message) use ($runData, $runsChannel, $interaction) {
                    $runData->channel_id = $runsChannel->id;
                    $runData->dmessage_id = $message->id;
                    $runData->dmessage_link = "https://discord.com/channels/" . $message->guild_id . "/" . $message->channel_id . "/" . $message->id;
                    $runData->save();

                }, function ($e) {
                    Log::error($e);
                });
            }
        }

    }

    protected function runsText($runData) {
        $text = "**" . $runData->count . "×" . $runData->level . " " . ucfirst($runData->adv) . "**\n\n" .
        "**Run ID**: " . $runData->id . "\n" .
        "**Date**: " . $runData->created_at . "\n" .
        "**Pot**: " . $runData->pot . ucfirst($runData->unit) . "\n" .
        "**Cut**: " . $runData->price . ucfirst($runData->unit) . "\n" .
        "**Advertiser**: " . ucfirst($runData->adv) . "\n" .
        "**Dungeons**: " . strtoupper($runData->dungeons) . "\n\n" .
            "**Boosters**\n";

        foreach ($runData->boosters as $booster) {
            $text .= ucfirst($booster) . "\n";
        }

        if ($runData->note) {
            $text .= "\n**Note**: " . $runData->note;
        }

        return $text;
    }

    protected function runsTemplate($runData) {

        $text = $this->runsText($runData);

        $runData->message = $text;
        $runData->save();

        $embed = new Embed($this->discord);
        $embed->setTitle("**Butterfly Boost Attendance**")
            ->setColor(0x483868)
            ->setDescription($text)
            ->setFooter("Attendance by " . $runData->user->name)
            ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

        return $embed;
    }

    protected function findRun($reaction) {
        $run = Run::where('dmessage_id', $reaction->message_id)->first();
        return $run;
    }

    protected function handleReaction($reaction, $run) {
        switch ($reaction?->emoji) {
        case '✅':
            Log::info($run);
            if ($run) {
                sleep(6);
                $this->changePaidRun($run, $reaction);
            }
            break;
        // case '❌':
        //     $run->paid = 0;
        //     $run->deleted_at = now();
        //     $run->save();
        //     break;

        default:
            # code...
            break;
        }
    }

    protected function showBalance($message, $isToday = false) {

        $username = $this->authUser->username;

        $nicknames = [
            'kallagh' => ['mmdraven', 'raven', 'mamadraven', 'kallagh'],
            'funn3r' => ['funn3r', 'funner'],
        ];

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
            $startTime = $this->customDay()['startTime'];
            $endTime = $this->customDay()['endTime'];
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

        $embed = new Embed($this->discord);
        $embed->setTitle("**Butterfly Boost Balance**")
            ->setColor(0x483868)
            ->setDescription($text)
            ->setFooter("Balance Of " . $this->authUser->name)
            ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

        $messageBuilder = MessageBuilder::new ()
            ->setContent('')
            ->addEmbed($embed);

        $message->reply($messageBuilder);
    }

    protected function myUnPaids($message) {

        $runs = Run::where('paid', 0)->cursor();

        $text = $this->runLogs($runs);

        $filePath = 'unpaid_boosts.txt';
        file_put_contents($filePath, $text);

        $messageBuilder = MessageBuilder::new ()
            ->addFile($filePath, 'unpaid_boosts.txt');

        $message->reply($messageBuilder);
    }

    protected function myAdds($message) {
        $user = User::find($this->authUser->id);
        $runs = $user->runs()->where('paid', 0)->cursor();

        $text = $this->runLogs($runs);

        $filePath = 'unpaid_boosts.txt';
        file_put_contents($filePath, $text);

        $messageBuilder = MessageBuilder::new ()
            ->addFile($filePath, 'unpaid_boosts.txt');

        $message->reply($messageBuilder);
    }

    protected function runLogs($runs) {
        $text = sprintf("%-3s | %-20s | %-6s | %-8s | %-45s | %-20s | %-12s\n",
            "ID", "Info", "Pot", "Cut", "Boosters", 'Dungeons', "Date");
        $text .= str_repeat("-", 132) . "\n";

        foreach ($runs as $run) {
            $boostersName = collect($run->boosters)->flatMap(fn($booster) => [ucfirst($booster)])->join('-');

            $unit = strtoupper($run->unit);
            $advName = ucfirst($run->adv);
            $date = Carbon::parse($run->updated_at)->format('m-d H:i');

            $text .= sprintf("%-3s | %-20s | %-6s | %-8s | %-45s | %-20s | %-12s\n",
                $run->id, "{$run->count}x{$run->level} {$advName}",
                "{$run->pot}{$unit}", "{$run->price}{$unit}",
                $boostersName, $run->dungeons, $date);
        }

        $text .= str_repeat("-", 132) . "\n";

        return $text;
    }

    protected function customDay() {
        $currentDate = Carbon::now()->format('Y-m-d');
        $tomarrowDate = Carbon::now()->addDay(1)->format('Y-m-d');

        $startTime = $currentDate . " 07:00:00";
        $endTime = $tomarrowDate . " 06:59:59";

        return [
            'startTime' => $startTime,
            'endTime' => $endTime,
        ];
    }
}
