<?php

namespace Flavorly\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

use function Laravel\Prompts\confirm;

final class HorizonWipeCommand extends Command
{
    protected $signature = 'horizon:wipe';

    protected $description = 'Flush everything in the queue and clears horizon';

    public function handle(): int
    {
        $confirmed = confirm('Are you really sure? This will wipe everything from the queue.');

        if (! $confirmed) {
            return self::FAILURE;
        }

        Redis::connection()->del('horizon:failed:*');
        Redis::connection()->del('horizon:failed_jobs');
        // @phpstan-ignore-next-line
        Redis::connection(name: 'horizon')->client()->flushAll();
        Artisan::call('horizon:clear');
        Artisan::call('horizon:clear-metrics');
        Artisan::call('horizon:purge');
        Artisan::call('queue:flush');

        return self::SUCCESS;
    }
}
