<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay;

use RuntimeException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Websystem\Gpwebpay\Commands\GpwebpayCommand;
use Websystem\Gpwebpay\Services\FileKeyLoader;

class GpwebpayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('webpay')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(GpwebpayCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(Gpwebpay::class, function () {
            $privateKeyPath = config('webpay.private_key_path');
            $publicKeyPath = config('webpay.public_key_path');
            $privateKeyPassword = config('webpay.private_key_password');
            $merchantNumber = config('webpay.merchant_number');
            $webpayUrl = config('webpay.url');

            $this->assertKeyFileExistsAndIsReadable($privateKeyPath, 'private');
            $this->assertKeyFileExistsAndIsReadable($publicKeyPath, 'public');

            $keyLoader = new FileKeyLoader($privateKeyPath, $privateKeyPassword, $publicKeyPath);

            return new Gpwebpay($keyLoader, $merchantNumber, $webpayUrl);
        });
    }

    private function assertKeyFileExistsAndIsReadable(string $filePath, string $type): void
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException(ucfirst($type)." key file {$filePath} does not exist!");
        }

        if (! is_readable($filePath)) {
            throw new RuntimeException(ucfirst($type)." key file {$filePath} is not readable!");
        }
    }
}
