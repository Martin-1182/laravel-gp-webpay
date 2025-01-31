<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Codehub\Gpwebpay\Commands\GpwebpayCommand;

class GpwebpayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('gp-webpay-sdk')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_gp_webpay_sdk_table')
            ->hasCommand(GpwebpayCommand::class);
    }
}
