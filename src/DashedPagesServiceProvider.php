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
            __DIR__ . '/../resources/templates' => resource_path('views/' . config('dashed-core.site_theme')),
        ], 'dashed-templates');

        cms()->registerRouteModel(Page::class, 'Pagina', 'Pagina\'s');

        $package
            ->name('dashed-pages');

        cms()->builder('plugins', [
            new DashedPagesPlugin(),
        ]);
    }
}
