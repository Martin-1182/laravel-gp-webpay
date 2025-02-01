<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Commands;

use Illuminate\Console\Command;

class GpwebpayCommand extends Command
{
    public $signature = 'webpay';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
