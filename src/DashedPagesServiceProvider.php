<?php

namespace Dashed\DashedPages;

use Dashed\DashedPages\Models\Page;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DashedPagesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-pages';

    public function bootingPackage(): void
    {
        Gate::policy(\Dashed\DashedPages\Models\Page::class, \Dashed\DashedPages\Policies\PagePolicy::class);

        cms()->registerRolePermissions('Pagina\'s', [
            'view_page' => 'Pagina\'s bekijken',
            'edit_page' => 'Pagina\'s bewerken',
            'delete_page' => 'Pagina\'s verwijderen',
        ]);
    }

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../resources/templates' => resource_path('views/' . config('dashed-core.site_theme', 'dashed')),
        ], 'dashed-templates');

        cms()->registerRouteModel(Page::class, 'Pagina', 'Pagina\'s');

        $package
            ->name('dashed-pages');

        cms()->builder('plugins', [
            new DashedPagesPlugin(),
        ]);
    }
}
