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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;

class DiscordBotController extends Controller {
    protected $discord;
    protected $authUser;
    protected $message;
    protected $nicknames;
    protected $commands;
    protected $timerRefrence = null;

    public function __construct() {
        $this->discord = new Discord([
            'token' => env('DISCORD_BOT_TOKEN'),
            'intents' => Intents::GUILDS | Intents::GUILD_MESSAGES | Intents::MESSAGE_CONTENT | Intents::DIRECT_MESSAGES | Intents::GUILD_MESSAGE_REACTIONS,
        ]);

        $this->commands = $this->getCommands();
    }

    public function startBot() {

        $this->discord->on('init', function (Discord $discord) {
            Log::info('Bot is ready!');
            $this->botServiceProvider();

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
            $this->botServiceProvider();

            if ($this->checkUser($message)) {
                $this->message = $message;
                $this->checkCommands($message);
            }

        });

        $this->discord->on(Event::MESSAGE_REACTION_ADD, function ($reaction, Discord $discord) {
            $this->botServiceProvider();

            if (!$reaction?->member?->user->bot) {
                if ($this->checkUser($reaction, 'reaction')) {
                    $run = $this->findRun($reaction);
                    $this->handleReaction($reaction, $run);
                }
            }

        });

        $this->discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
            $this->botServiceProvider();

            $messageBuilder = MessageBuilder::new ();
            if (!$this->checkUser($interaction, 'command')) {
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

    protected function botServiceProvider() {
        $this->nicknames = $this->getNicknames();
    }

    protected function checkUser($message, $method = 'message') {

        $author = match ($method) {
            'message' => $message->author->id,
            'command' => $message->user->id,
            'reaction' => $message->user_id,
            default => null,
        };

        $this->authUser = User::where('duser_id', $author)->first();
        return $this->authUser !== null;
    }

    protected function getCommands() {

        $commands = [
            '!aadmin' => [
                'regex' => [
                    'pattern' => '/!aadmin .+ .+ .+/',
                    'message' => '!aadmin <username> <dusuer_id> <name>',
                ],
                'callback' => [$this, 'addAdmin'],
                'usage' => 'Adds new Admin',
            ],

            '!radmin' => [
                'regex' => [
                    'pattern' => '/!radmin .+/',
                    'message' => '!radmin <duserid>',
                ],
                'callback' => [$this, 'removeAdmin'],
                'usage' => 'Removes a Admin',
            ],

            '!anick' => [
                'regex' => [
                    'pattern' => '/^!anick [^\s]+ [^\s]+$/',
                    'message' => '!anick <username> <nicknames (sep:-)>',
                ],
                'callback' => [$this, 'addNicknames'],
                'usage' => 'Adds nickname for a admin, nicknames are uses for balance check and attendaces',
            ],

            '!arun' => [
                'regex' => [
                    'pattern' => '/^!arun .+ \d+[xX]\d+ [A-Z0-9a-z\-]+ \d+[tTkK] -> (?:.+-)*\w+(?: .*)?$/',
                    'message' => '!arun <adv> <count>x<level> <dungeons> <pot><t/k> -> <boosters (ex:bob-alex-john-clark)> <?note>',
                ],
                'callback' => [$this, 'addRun'],
                'usage' => 'Adds new run attendance',
            ],

            '!erun' => [
                'regex' => [
                    'pattern' => '/^!erun \d+ .+ \d+[xX]\d+ [A-Z0-9a-z\-]+ \d+[tTkK] -> (?:.+-)*\w+(?: .*)?$/',
                    'message' => '!erun <runid> <adv> <count>x<level> <dungeons> <pot><t/k> -> <boosters (ex:bob-alex-john-clark)> <?note>',
                ],
                'callback' => [$this, 'editRun'],
                'usage' => 'edits a run attendance',
            ],

            '!rrun' => [
                'regex' => [
                    'pattern' => '/!rrun \w+/',
                    'message' => '!rrun <runid>',
                ],
                'callback' => [$this, 'removeRun'],
                'usage' => 'removes a run attendance',
            ],

            '!srun' => [
                'regex' => [
                    'pattern' => '/^!srun \d+$/',
                    'message' => '!srun <runid>',
                ],
                'callback' => [$this, 'showRun'],
                'usage' => 'shows a run attendance',
            ],

            '!eruser' => [
                'regex' => [
                    'pattern' => '/^!eruser \d+ \w+/',
                    'message' => '!eruser <runid> <username>',
                ],
                'callback' => [$this, 'changeRunUser'],
                'usage' => 'Changes the user of the run',
            ],

            '!!b' => [
                'callback' => [$this, 'showBalance'],
                'usage' => 'Shows Balance',
            ],

            '!!bt' => [
                'callback' => [$this, 'showBalance'],
                'parameters' => true,
                'usage' => 'shows today balance',
            ],

            '!unpaids' => [
                'callback' => [$this, 'unPaids'],
                'usage' => 'shows unpaids runs attendace sheet, can be with [unit, adv, level, user] ex: !unpaids adv',
            ],

            '!myadds' => [
                'callback' => [$this, 'myAdds'],
                'usage' => 'shows unpaids runs added by yourself, can be with [unit, adv, level, user] ex: !myadds adv',
            ],

            '!givemedb' => [
                'callback' => [$this, 'sendDbBackup'],
                'usage' => 'giving the backup of the runs',
                'perm' => 10,
            ],

            '!givemedbjson' => [
                'callback' => [$this, 'sendDbJson'],
                'usage' => 'giving the json backup of the runs',
                'perm' => 10,
            ],

            '!clearcaches' => [
                'callback' => [$this, 'clearCaches'],
                'usage' => 'Clears the caches',
                'perm' => 10,
            ],

            '!help' => [
                'callback' => [$this, 'commandsHelper'],
                'usage' => 'Shows the list of the commands',
            ],

            '!importruns' => [
                'callback' => [$this, 'importRuns'],
                'usage' => 'Imports Runs by Json',
                'perm' => 10,
            ],

            // '!announceallpaids' => ['callback' => [$this, 'announcePaidRuns']],
        ];

        return $commands;
    }

    protected function commandsHelper($message) {
        $text = '';

        $publicCommands = array_filter($this->commands, function ($command) {
            return !isset($command['perm']);
        });

        foreach ($publicCommands as $command => $commandInfo) {
            if (!isset($commandInfo['usage'])) {
                continue;
            }

            $text .= "**{$command}** : " . ucfirst($commandInfo['usage']) . "\n\n";
        }

        if ($this->authUser->username === env('DISCORD_BOT_OWNER_USERNAME')) {
            $privateCommands = array_filter($this->commands, function ($command) {
                return isset($command['perm']);
            });

            $text .= "\n\n";
            foreach ($privateCommands as $command => $commandInfo) {
                if (!isset($commandInfo['usage'])) {
                    continue;
                }

                $text .= "**{$command}** : " . ucfirst($commandInfo['usage']) . "\n";
            }
        }

        $embed = new Embed($this->discord);
        $embed->setTitle("**Bot Commands Helper**")
            ->setColor(0xFDD835)
            ->setDescription($text);

        $messageBuilder = MessageBuilder::new ()
            ->setContent('')
            ->addEmbed($embed);

        $message->reply($messageBuilder);
    }

    protected function checkCommands($message) {
        $userCommand = str($message->content)->trim()->lower()->before(' ')->toString();

        if (isset($this->commands[$userCommand]) && !empty($this->commands[$userCommand])) {
            $command = $this->commands[$userCommand];

            if (isset($command['regex'])) {

                if (!preg_match($command['regex']['pattern'], $message->content)) {
                    return $message->reply($command['regex']['message']);
                }
            }

            return call_user_func($command['callback'], $message, $command['parameters'] ?? null); // calling the callback of command
        }

        return null;
    }

    protected function addAdmin($message) {

        $messageContent = $message->content;

        $messageContent = explode(' ', $messageContent);
        unset($messageContent[0]);
        $messageContent = array_filter($messageContent);
        $messageContent = array_values($messageContent);

        $newUserData['username'] = $messageContent[0];
        $newUserData['duser_id'] = $messageContent[1];
        $newUserData['name'] = $messageContent[2];

        //region Add nickname to user with add admin
        // if (isset($messageContent[3]) && ! empty($messageContent[3])) {
        //     $nicknames = $messageContent[3]
        //     $newUserData['nicknames'] = $messageContent[3];
        // }
        //endregion

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
            $message->reply('User not found');
        }
    }

    protected function updateRun($run, $runData, $reply = false) {

        $authUser = $this->authUser;

        $runText = $this->runsText($runData);
        $run->update(array_merge($runData, ['message' => $runText]));

        $payUser = User::where('id', $run->pay_user)->first();

        $text = $runText;
        $text .= "\n**Edited by **<@{$authUser->duser_id}>";
        $text .= "\n**Edited at**: " . $run->updated_at;

        $color = 0x483868;
        if ($run->paid) {
            $color = 0x4caf50;
            $text .= "\n\n✅ **Run paid by <@{$payUser->duser_id}>** ✅";
            $text .= "\n**Paid at**: " . $run->paid_at;
        }

        $embed = new Embed($this->discord);
        $embed->setTitle("**Butterfly Boost Attendance**")
            ->setColor($color)
            ->setDescription($text)
            ->setFooter("Attendance by {$run->user->name}")
            ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

        $messageBuilder = MessageBuilder::new ()
            ->setContent('')
            ->addEmbed($embed);

        $channel = $this->discord->getChannel($run->channel->dchannel_id);
        $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($messageBuilder) {
            $discordMessage->edit($messageBuilder);
        })->otherwise(function () use ($channel, $messageBuilder, $run) {
            $channel->sendMessage($messageBuilder)->then(function ($sentMessage) use ($run) {
                $run->dmessage_id = $sentMessage->id;
                $run->dmessage_link = "https://discord.com/channels/" . $sentMessage->guild_id . "/" . $sentMessage->channel_id . "/" . $sentMessage->id;
                $run->save();
            });
        });

        if ($reply) {
            $this->message->reply("Run edited successfully " . $run->dmessage_link);
        }
    }

    protected function handleAddRunCommand($message) {
        $messageContent = $message->content;

        $messageContent = trim($messageContent);

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
                'channel_id' => $runsChannel->id,
                'dmessage_id' => null,
                'dmessage_link' => null,
            ]);

            if ($run) {
                $this->announceRuns($run, $runsChannel)->then(function ($runData) use ($message) {
                    $message->reply("Run added successfully " . $runData->dmessage_link);
                })->otherwise(function () use ($message) {
                    $message->reply("Run added successfully");
                });
            } else {
                $message->reply('Run not added');
            }
        });
    }

    protected function removeRun($message) {

        DB::transaction(function () use ($message) {
            $messageContent = $message->content;

            $authUser = $this->authUser;

            $messageContent = explode(' ', $message->content);
            unset($messageContent[0]);
            $runId = $messageContent[1];

            $run = Run::find($runId);

            if ($run) {
                $channel = $this->discord->getChannel($run->channel->dchannel_id);

                $text = $run->message;
                $text .= "\n\n❌ **Run removed by " . "<@{$authUser->duser_id}>" . '** ❌';

                $embed = new Embed($this->discord);
                $embed->setTitle("**Butterfly Boost Attendance**")
                    ->setColor(0xdf3079)
                    ->setDescription($text)
                    ->setFooter("Attendance by {$authUser->name}")
                    ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

                $messageBuilder = MessageBuilder::new ()
                    ->setContent('')
                    ->addEmbed($embed);

                $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($run, $authUser, $messageBuilder) {
                    $discordMessage->edit($messageBuilder)->then(function ($message) {
                        $message->react('❌');
                    });
                })->otherwise(function ($error) use ($messageBuilder, $channel, $run) {
                    $channel->sendMessage($messageBuilder)->then(function ($messageSent) use ($run) {
                        $run->dmessage_id = $messageSent->id;
                        $run->dmessage_link = "https://discord.com/channels/" . $messageSent->guild_id . "/" . $messageSent->channel_id . "/" . $messageSent->id;
                        $run->save();

                        $messageSent->react('❌');
                    });
                });

                $run->delete();
                $message->reply('Run removed successfully ' . $run->dmessage_link);
            } else {
                $message->reply('Run not removed');
            }
        });
    }

    protected function editRun($message) {

        DB::transaction(function () use ($message) {

            $messageContent = $message->content;

            $messageContent = trim($messageContent);

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

                $runData = [
                    'id' => $run->id,
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
                    'created_at' => $run->created_at,
                ];

                $this->updateRun($run, $runData, true);
            } else {
                return $message->reply("Invalid Run ID");
            }
        });

    }

    protected function showRun($message) {
        $messageContent = $message->content;

        $messageContent = trim($messageContent);

        $messageContent = explode(' ', $messageContent);
        $runId = $messageContent[1];

        $run = Run::find($runId);

        if ($run) {
            $text = $run->message;

            if ($run->paid == 1) {
                $text .= "\n\n✅ **Run paid by <@" . $run->payUser->duser_id . '>** ✅';
                $text .= "\n**Paid at**: " . $run->paid_at;
            }

            $embed = new Embed($this->discord);
            $embed->setTitle("**Butterfly Boost Attendance**")
                ->setColor(0x483868)
                ->setDescription($text)
                ->setFooter("Attendance by {$run->user->name}")
                ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

            $messageBuilder = MessageBuilder::new ()
                ->setContent('')
                ->addEmbed($embed);
            $message->reply($messageBuilder);
        }
    }

    protected function addRunWithCommand($interaction) {
        $runsChannel = Channel::where('channel_name', 'runs')->firstOrFail();
        $interaction->respondWithMessage("Processing your request... " . $runsChannel?->channel_link ?? '', true);

        DB::transaction(function () use ($interaction, $runsChannel) {
            $options = $interaction->data->options;
            $boostersName = explode('-', $options['boosters_name']->value);
            $boostersCount = count($boostersName);

            $runPot = (int) $options['run_pot']->value;
            $runPrice = number_format($runPot / $boostersCount, 2);
            $runUnit = $options['unit']->value ?? '?';

            $run = Run::create([
                'count' => $options['run_count']->value,
                'level' => $options['run_level']->value,
                'dungeons' => $options['dungeons']->value,
                'boosters' => $boostersName,
                'boosters_count' => $boostersCount,
                'price' => $runPrice,
                'unit' => $runUnit,
                'pot' => $runPot,
                'adv' => $options['advertiser']->value,
                'note' => $options['additional_note']?->value,
                'user_id' => $this->authUser->id,
                'channel_id' => $runsChannel->id,
                'dmessage_id' => null,
                'dmessage_link' => null,
            ]);

            $this->announceRuns($run, $runsChannel, $interaction);
        });

    }

    protected function editRunWithCommand($interaction) {
        $runsChannel = Channel::where('channel_name', 'runs')->first();
        $interaction->respondWithMessage("Processing your request... " . $runsChannel->channel_link ?? '', true);

        DB::transaction(function () use ($interaction) {
            $options = $interaction->data->options;

            $boostersName = explode('-', $options['boosters_name']->value);
            $boostersCount = count($boostersName);

            $runPot = (int) $options['run_pot']->value;
            $runPrice = $runPot / $boostersCount;
            $runPrice = number_format($runPrice, 2);

            $runUnit = $options['unit']->value;

            $runId = $options['run_id']->value;

            $run = Run::find($runId);

            if ($run) {

                $runData = [
                    'id' => $run->id,
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
                    'created_at' => $run->created_at,
                ];

                $this->updateRun($run, $runData);
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
        $run->pay_user = $this->authUser->id;
        $run->save();

        $payUser = $reaction->member;

        $this->sendPaidToUser($run);

        $channel = $this->discord->getChannel($run->channel->dchannel_id);
        $channel->messages->fetch($run->dmessage_id)->then(function ($discordMessage) use ($run, $payUser) {

            $text = $run->message;
            $text .= "\n\n✅ **Run paid by " . $payUser . '** ✅';
            $text .= "\n**Paid at**: " . $run->paid_at;

            $embed = new Embed($this->discord);
            $embed->setTitle("**Butterfly Boost Attendance**")
                ->setColor(0x4caf50)
                ->setDescription($text)
                ->setFooter("Attendance by {$run->user->name}")
                ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

            $messageBuilder = MessageBuilder::new ()
                ->setContent('')
                ->addEmbed($embed);

            $discordMessage->edit($messageBuilder)->then(function ($message) {
                $message->react('✅');
            });

            $this->sendToPaidChannel($messageBuilder);
        }, function ($error) {
            echo "Error fetching message: " . $error->getMessage();
        });

    }

    protected function sendToPaidChannel($messageBuilder) {
        $paidChannel = Channel::where('channel_name', 'paid_channel')->first();
        $channel = $this->discord->getChannel($paidChannel->dchannel_id);

        if ($channel) {
            $promise = $channel->sendMessage($messageBuilder);

            $promise->then(function ($message) {
                $message->react('✅');
            });
        }

    }

    protected function announceRuns($runData, $runsChannel) {

        if ($runsChannel) {

            $channel = $this->discord->getChannel($runsChannel->dchannel_id);
            if ($channel) {
                $promise = $channel->sendMessage('', false, $this->runsTemplate($runData));

                return $promise->then(function ($message) use ($runData, $runsChannel) {
                    $runData->channel_id = $runsChannel->id;
                    $runData->dmessage_id = $message->id;
                    $runData->dmessage_link = "https://discord.com/channels/" . $message->guild_id . "/" . $message->channel_id . "/" . $message->id;
                    $runData->save();

                    return $runData;
                }, function ($e) {
                    return null;
                    Log::error($e);
                });
            }
        }

    }

    protected function runsText($runData) {

        if (gettype($runData) != 'object') {
            $runData = (object) $runData;
        }

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
            ->setFooter("Attendance by {$runData->user->name}")
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
            if ($run) {
                $this->changePaidRun($run, $reaction);
            }
            break;
        default:
            # code...
            break;
        }
    }

    protected function showBalance($message, $isToday = false) {

        $username = $this->authUser->username;

        $rows = DB::table('runs')
            ->where('deleted_at', null);

        $nicknames = array_filter($this->nicknames, function ($nickname) use ($username) {
            return $nickname === $username;
        }, ARRAY_FILTER_USE_KEY);

        if (empty($nicknames)) {
            $message->reply("You dont have balance account!");
            return false;
        }

        foreach ($nicknames as $nickname => $nicknamesArray) {

            if (empty($nicknamesArray)) {
                $message->reply("You dont have balance account!");
                return false;
            }

            $rows->where(function ($query) use ($nicknamesArray) {
                foreach ($nicknamesArray as $nickname) {
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

    protected function unPaids($message) {

        $groupBy = $this->extractOrderBy($message);

        $runs = Run::where('paid', 0)
            ->with('user')
            ->get();

        $text = $this->runLogs($runs, $groupBy);

        $filePath = 'unpaid_boosts.txt';
        file_put_contents($filePath, $text);

        $messageBuilder = MessageBuilder::new ()
            ->addFile($filePath, 'unpaid_boosts.txt');

        $message->reply($messageBuilder);

        unlink($filePath);
    }

    protected function myAdds($message) {

        $groupBy = $this->extractOrderBy($message);

        $user = User::find($this->authUser->id);
        $runs = $user->runs()
            ->with('user')
            ->where('paid', 0)
            ->get();

        $text = $this->runLogs($runs, $groupBy);

        $filePath = 'unpaid_boosts.txt';
        file_put_contents($filePath, $text);

        $messageBuilder = MessageBuilder::new ()
            ->addFile($filePath, 'unpaid_boosts.txt');

        $message->reply($messageBuilder);

        unlink($filePath);
    }

    protected function extractOrderBy($message) {
        $messageContent = $message->content;
        $messageArray = explode(' ', $messageContent);
        $param = null;

        if (count($messageArray) > 1) {
            $param = $messageArray[1];

            $params = collect(['unit', 'adv', 'count', 'level', 'user']);

            if (!$params->contains($param)) {
                $param = '';
            }

            if ($param == 'user') {
                $param = 'user.name';
            }
        }

        return $param;
    }

    protected function runLogs($runs, $groupBy = '') {
        if (!empty($groupBy)) {
            $grouped = $runs->groupBy($groupBy);
        } else {
            $grouped = collect([$runs]);
        }

        $textFormat = "%-3s | %-20s | %-13s | %-6s | %-8s | %-45s | %-20s | %-12s\n";

        $text = sprintf($textFormat,
            "ID", "Info", "User", "Pot", "Cut", "Boosters", 'Dungeons', "Date");
        $text .= str_repeat("-", 150) . "\n";

        foreach ($grouped as $groupKey => $groupRuns) {
            $groupBySum = 0;
            if (!empty($groupBy)) {

                $groupByTitle = $groupBy == 'user.name' ? 'User' : $groupBy;

                $text .= ucfirst($groupByTitle) . ": " . ucfirst($groupKey) . "\n";
            }

            foreach ($groupRuns as $run) {
                $boostersName = collect($run->boosters)->flatMap(fn($booster) => [ucfirst($booster)])->join('-');
                $unit = strtoupper($run->unit);

                $advName = ucfirst($run->adv);

                $date = Carbon::parse($run->updated_at)->format('m-d H:i');

                $text .= sprintf($textFormat,
                    $run->id, "{$run->count}x{$run->level} {$advName}", ucfirst($run->user->name),
                    "{$run->pot}{$unit}", "{$run->price}{$unit}",
                    $boostersName, $run->dungeons, $date);

            }

            if (!empty($groupBy)) {

                $sumT = $groupRuns->whereIn('unit', ['t', 'T'])->sum('pot');
                $sumK = $groupRuns->whereIn('unit', ['k', 'K'])->sum('pot');
                $count = $groupRuns->count();

                $text .= "\n" . sprintf($textFormat,
                    '#', "Total: ", $count,
                    "{$sumT}T", "{$sumK}K",
                    '-', '-', '-');
            }

            $text .= str_repeat("-", 150) . "\n";
        }

        return $text;
    }

    protected function customDay() {
        $now = Carbon::now();

        if ($now->hour < 7) {
            $startTime = Carbon::yesterday()->setHour(7)->setMinute(0)->setSecond(0);
            $endTime = Carbon::today()->setHour(6)->setMinute(59)->setSecond(59);
        } else {
            $startTime = Carbon::today()->setHour(7)->setMinute(0)->setSecond(0);
            $endTime = Carbon::tomorrow()->setHour(6)->setMinute(59)->setSecond(59);
        }

        $startTime = $startTime->format('Y-m-d H:i:s');
        $endTime = $endTime->format('Y-m-d H:i:s');

        return [
            'startTime' => $startTime,
            'endTime' => $endTime,
        ];
    }

    private function sendDbBackup($message) {

        if ($this->authUser->username != 'funn3r') {
            return false;
        }

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

        $backupPath = 'runs_discord-bot.sql';
        file_put_contents($backupPath, $backupSql);

        $messageBuilder = MessageBuilder::new ()
            ->addFile($backupPath, 'runs_discord-bot.sql');
        $message->reply($messageBuilder);

        unlink($backupPath);
    }

    private function sendDbJson($message) {

        if ($this->authUser->username != 'funn3r') {
            return false;
        }

        $runs = Run::withTrashed()->cursor();

        $jsonData = [];
        foreach ($runs as $run) {
            $jsonData[] = $run->toArray();
        }

        $jsonOutput = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $backupPath = 'runs_discord-bot.json';
        file_put_contents($backupPath, $jsonOutput);

        $messageBuilder = MessageBuilder::new ()
            ->addFile($backupPath, 'runs_discord-bot.json');
        $message->reply($messageBuilder);

        unlink($backupPath);
    }

    protected function announcePaidRuns() {
        $paidChannel = Channel::where('channel_name', 'paid_channel')->first();
        $channel = $this->discord->getChannel($paidChannel->dchannel_id);

        $runs = Run::where('paid', 1)->get();
        $delay = 0;
        foreach ($runs as $run) {

            if ($run->pay_user == null) {
                $run->pay_user = $run->user_id;
                $run->save();
            }

            $text = $run->message;
            $text .= "\n\n✅ **Run paid by** <@{$run->payUser->duser_id}> ✅";
            $text .= "\n**Paid at**: " . $run->paid_at;

            $embed = new Embed($this->discord);
            $embed->setTitle("**Butterfly Boost Attendance**")
                ->setColor(0x4caf50)
                ->setDescription($text)
                ->setFooter("Attendance by {$run->user->name}")
                ->setThumbnail('https://cdn.discordapp.com/icons/878241085535715380/33780e7fe9cf2f42db8a6083f0f8bc5d.webp?size=1024');

            $messageBuilder = MessageBuilder::new ()
                ->setContent('')
                ->addEmbed($embed);

            if ($channel) {

                Loop::addTimer(3, function () use ($channel, $messageBuilder) {
                    $promise = $channel->sendMessage($messageBuilder);

                    $promise->then(function ($message) {
                        $message->react('✅');
                    });

                });

                $delay += 1;
            }
        }
    }

    protected function sendPaidToUser($runData) {

        if ($this->timerRefrence instanceof TimerInterface) {
            $this->discord->getLoop()->cancelTimer($this->timerRefrence);
        }

        $boosters = $runData->boosters;
        $users = User::all();

        $boostersPayment = [];
        foreach ($this->nicknames as $username => $nicknameArray) {

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

        $paidInfo = [];

        foreach ($boostersPayment as $boosterName => $boosterCount) {
            $user = $users->where('username', $boosterName)->first();

            $this->discord->users->fetch($user->duser_id)->then(function ($user) use ($runData, $boosterCount) {
                $text = "**New Run Paid By** <@{$runData->payUser->duser_id}>\n";
                $text .= "**Run ID**: " . $runData->id . "\n";
                $text .= "**Cut**: " . ((int) $runData->price * $boosterCount) . ucfirst($runData->unit) . "\n";
                $text .= $runData->dmessage_link;
                $user->sendMessage($text);

            }, function ($error) {
                return false;
            });

            $totalCutK = 0;
            $totalCutT = 0;

            if (strtolower($runData->unit) == 't') {
                $totalCutT += (int) $runData->price * $boosterCount;
            } else if (strtolower($runData->unit) == 'k') {
                $totalCutK += (int) $runData->price * $boosterCount;
            }

            if (isset($paidInfo[$boosterName])) {
                $paidInfo[$boosterName] = [
                    'totalCutT' => $paidInfo[$boosterName]['totalCutT'] + $totalCutT,
                    'totalCutK' => $paidInfo[$boosterName]['totalCutK'] + $totalCutK,
                ];

            } else {
                $paidInfo[$boosterName] = [
                    'totalCutT' => $totalCutT,
                    'totalCutK' => $totalCutK,
                    'duser_id' => $user->duser_id,
                ];

            }
        }

        if ($oldPaidInfo = Cache::get('paidInfo')) {
            Cache::forget('paidInfo');

            foreach ($oldPaidInfo as $oldPaidUser => $oldPaidValue) {
                if (isset($paidInfo[$oldPaidUser])) {
                    $paidInfo[$oldPaidUser]['totalCutT'] += $oldPaidValue['totalCutT'];
                    $paidInfo[$oldPaidUser]['totalCutK'] += $oldPaidValue['totalCutK'];
                } else {
                    $paidInfo[$oldPaidUser] = [
                        'totalCutT' => $oldPaidValue['totalCutT'],
                        'totalCutK' => $oldPaidValue['totalCutK'],
                        'duser_id' => $oldPaidValue['duser_id'],
                    ];
                }
            }

        }

        Cache::put('paidInfo', $paidInfo, 60);

        $this->timerRefrence = $this->discord->getLoop()->addTimer(60, function () use ($paidInfo) {
            $this->sendTotalPaid($paidInfo);
            Log::info('Timer Executed');
        });
    }

    protected function addNicknames($message) {
        $messageContent = $message->content;

        $messageContent = str($messageContent)->replace('!anick', '');

        $params = explode(' ', $messageContent);

        $username = $params[1];

        $nicknames = $params[2];
        $nicknames = explode('-', $nicknames);

        $user = User::whereUsername($username)->first();

        if (!$user) {
            return $message->reply("The username is invalid");
        }

        $userNicknames = (array) $user->nicknames;

        $newNicknames = collect(array_merge($userNicknames, $nicknames))->unique()->toArray();
        $user->update(['nicknames' => $newNicknames]);

        $text = ucfirst($username) . "'s nicknames updated successfully! ✅\n";
        $text .= "New nicknames: [" . implode('-', $newNicknames) . ']';

        Cache::forget('nicknames');

        $message->reply($text);
    }

    protected function getNicknames() {
        if (!$nicknames = Cache::get('nicknames')) {
            $nicknames = User::pluck('nicknames', 'username')->toArray();
            Cache::put('nicknames', $nicknames, now()->addDay());
        }

        return $nicknames;
    }

    protected function clearCaches($message) {
        Cache::forget('nicknames');
        $message->reply('Caches Cleared!');
    }

    protected function sendTotalPaid($paidInfo) {
        foreach ($paidInfo as $user => $paidData) {
            $this->discord->users->fetch($paidData['duser_id'])->then(function ($user) use ($paidData) {
                $text = "**Latest Paids:**\n";
                $text .= "💵 " . $paidData['totalCutT'] . "T\n";
                $text .= "🪙 " . $paidData['totalCutK'] . "K";
                $user->sendMessage($text);
            })->otherwise(function ($error) {
                Log::info($error);
            });
        }
    }

    protected function changeRunUser($message) {
        $messageContent = $message->content;

        $messageContent = str($messageContent)->replace('!eruser', '');

        $params = explode(' ', $messageContent);

        $runId = $params[1];
        $username = $params[2];

        $run = Run::find($runId);

        if (!$run) {
            $message->reply("The **RunId** is invalid!");
        }

        $user = User::whereUsername($username)->first();

        if (!$user) {
            $message->reply("The **username** is invalid!");
        }

        $run->update([
            'user_id' => $user->id,
        ]);

        $message->reply("The run is now part of <@{$user->duser_id}>'s payments.");

    }

    protected function importRuns($message) {
        $filePath = public_path('runs_discord-bot.json');

        if (file_exists($filePath)) {
            $runsData = File::get($filePath);

            $jsonData = json_decode($runsData, true);

            foreach ($jsonData as $run) {
                Run::create($run);
            }

            $message->reply("Runs Imported!");
        } else {
            $message->reply("Invalid Json!");
        }

    }

}
