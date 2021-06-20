<?php

namespace App\Console;

use App\Console\Commands\Ims\SetupCommand;
use App\Console\Commands\Ims\SynchronizeCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
        SynchronizeCommand::class,
        SetupCommand::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
