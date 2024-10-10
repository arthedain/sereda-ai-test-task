<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all subscriptions and send emails if needed';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting subscriptions check');

        $service = new SubscriptionService();

        Team::with('subscriptions')->get()->each(function (Team $team) use ($service){
            if($team->subscriptions->count() > 0) {
                $service->processSubscriptions($team->subscriptions);
            }
        });


        $this->info('Subscriptions check finished');
    }
}
