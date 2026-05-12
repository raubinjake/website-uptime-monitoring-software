<?php

namespace App\Console\Commands;

use App\Jobs\CheckWebsiteUptime;
use App\Models\Website;
use Illuminate\Console\Command;

class MonitorWebsitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:websites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch uptime checks for all active websites.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = 0;

        Website::query()
            ->where('is_active', true)
            ->select('id')
            ->chunkById(100, function ($websites) use (&$count) {
                foreach ($websites as $website) {
                    CheckWebsiteUptime::dispatch($website->id);
                    $count++;
                }
            });

        $this->info("Dispatched {$count} website uptime checks.");

        return self::SUCCESS;
    }
}
