<?php

namespace Qubiqx\QcommercePages;

use Qubiqx\QcommercePages\Commands\QcommercePagesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QcommercePagesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('qcommerce-pages')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_qcommerce-pages_table')
            ->hasCommand(QcommercePagesCommand::class);
    }
}
