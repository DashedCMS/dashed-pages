<?php

namespace Dashed\DashedPages;

use Dashed\DashedPages\Models\Page;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DashedPagesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-pages';

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../resources/templates' => resource_path('views/' . env('SITE_THEME', 'dashed')),
        ], 'dashed-core-templates');

        cms()->builder(
            'routeModels',
            array_merge(cms()->builder('routeModels'), [
                'page' => [
                    'name' => 'Pagina',
                    'pluralName' => 'Pagina\'s',
                    'class' => Page::class,
                    'nameField' => 'name',
                ],
            ])
        );

        $package
            ->name('dashed-pages');
    }
}
