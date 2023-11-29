<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetEverythingCommand extends Command
{
    protected $signature = 'reset:everything';

    protected $description = 'Clears the database for quick testing';

    // https://ionline2.pipedrive.com/deal/199 - single deal
    // https://ionline2.pipedrive.com/deal/200 - 50/50 deal
    // https://ionline2.pipedrive.com/deal/202 - 50/25/25 deal
    // https://ionline2.pipedrive.com/deal/202 - variable
    public function handle(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Deal::truncate();
        \App\Models\Invoice::truncate();
        \App\Models\LineItem::truncate();
        \App\Models\Log::truncate();
        \App\Models\Payment::truncate();
        \App\Models\Transaction::truncate();
        \App\Models\User::where('id', '!=', 2)->delete();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
