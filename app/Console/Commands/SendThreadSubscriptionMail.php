<?php

namespace App\Console\Commands;

use App\Services\Board\ThreadNotificationMailer;
use Illuminate\Console\Command;

class SendThreadSubscriptionMail extends Command
{
    protected $signature = 'forum:send-subscription-mail';

    protected $description = 'Send due thread subscription emails and digests.';

    public function handle(ThreadNotificationMailer $mailer): int
    {
        $mailer->processPending();
        $this->info('Thread subscription mail processed.');

        return self::SUCCESS;
    }
}
