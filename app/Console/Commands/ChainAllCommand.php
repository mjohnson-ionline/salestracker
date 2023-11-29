<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChainAllCommand extends Command
{
    protected $signature = 'chain:all';

    protected $description = 'Runs all the commands in sequence';

    // https://ionline2.pipedrive.com/deal/199 - single deal
    // https://ionline2.pipedrive.com/deal/200 - 50/50 deal
    // https://ionline2.pipedrive.com/deal/202 - 50/25/25 deal
    // https://ionline2.pipedrive.com/deal/203 - variable
    public function handle(): void
    {
        $this->call('remove:duplicated-pipedrive-syncs');
        sleep(1);
        $this->call('generate:invoices-from-logs');
        sleep(1);
        $this->call('parse:logs-to-xero-payments');
    }
}
