<?php

namespace App\Console;

use App\Console\Commands\CheckToken;
use App\Console\Commands\FixData;
use App\Console\Commands\Notice;
use App\Console\Commands\Notify;
use App\Console\Commands\Push;
use App\Console\Commands\SendNotify;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckToken::class,
        Push::class,
        //Notice::class
        Notify::class,
        FixData::class,
        SendNotify::class
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('push')->everyMinute();
        $schedule->command('refreshToken')->everyMinute();
        $schedule->command('notify longtouhuan')->everyMinute();
        $schedule->command('notify huxun')->everyMinute();
        $schedule->command('notify xijiao')->everyMinute();
        $schedule->command('notify shiqi')->everyMinute();
        //$schedule->command('notice')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
