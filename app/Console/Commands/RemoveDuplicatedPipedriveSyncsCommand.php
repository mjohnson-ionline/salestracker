<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;

class RemoveDuplicatedPipedriveSyncsCommand extends Command
{
    protected $signature = 'remove:duplicated-pipedrive-syncs';

    protected $description = 'The pipedrive api will send the same api request to our endpoint with near identical data microseconds apart. This causes the system to run multiple times. This job removes all duplicated syncs so they can be processed singularly.';

    public function handle(): void
    {

        // get all logs with the type = pipedrive, action = deal.updated
        $logs = Log::where('type', 'deal.updated')
            ->where('platform', 'pipedrive')
            ->where('pipedrive_duplicate_parsed', false)
            ->get();

        // if there is more than 1 with the same webhook_id, remove them from the database
        foreach ($logs as $log) {

                $logs = Log::where('type', 'deal.updated')
                    ->where('platform', 'pipedrive')
                    ->where('webhook_id', $log->webhook_id)
                    ->where('pipedrive_duplicate_parsed', false)
                    ->where('id', '!=', $log->id)
                    ->get();

                if ($logs->count() > 1) {
                    $this->info('Removing duplicated logs with webhook_id: ' . $log->webhook_id);
                    $logs->each(function ($log) {
                        $log->transactions()->delete();
                        $log->delete();
                    });
                }

                // set the pipedrive_duplicate_parsed to true
                $log->pipedrive_duplicate_parsed = true;
                $log->save();
        }

    }
}
