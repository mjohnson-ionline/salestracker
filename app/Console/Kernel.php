<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*// run the scheule to send test emails
        // $schedule->command('send:test-email')->everyMinute();

        // run the command to sync pipedrive products day
        $schedule->command('sync:pipe-drive-products')->daily();

        // run the command to clean up any pipedrive duplicates every minute, then chain the command to generate any invoices from the logs
        $schedule->command('remove:duplicated-pipedrive-syncs')->everyMinute()->then(function () {
            $this->call('generate:invoices-from-logs');
        });

        // run the command to parse any logs from xero into payments every minute
        $schedule->command('parse:logs-to-xero-payments')->everyMinute();*/

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
