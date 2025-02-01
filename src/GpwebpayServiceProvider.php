<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay;

use Codehub\Gpwebpay\Commands\GpwebpayCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
