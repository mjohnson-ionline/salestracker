<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTestEmailCommand extends Command
{
    protected $signature = 'send:test-email';

    protected $description = 'Send a test email to make sure the scheduler is working.';

    public function handle(): void
    {
        mail('matthew@ionline.com.au', 'test scheduler', 'test scheduler');
    }
}
