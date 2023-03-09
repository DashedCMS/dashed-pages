<?php

namespace Qubiqx\QcommercePages;

use Filament\PluginServiceProvider;
use Qubiqx\QcommercePages\Classes\PageRouteHandler;
use Qubiqx\QcommercePages\Filament\Resources\PageResource;
use Qubiqx\QcommercePages\Models\Page;
use Spatie\LaravelPackageTools\Package;

class QcommercePagesServiceProvider extends PluginServiceProvider
{
    public static string $name = 'qcommerce-pages';

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        cms()->model('Page', Page::class);

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
            ->name('qcommerce-pages');
    }

    protected function getResources(): array
    {
        return array_merge(parent::getResources(), [
            PageResource::class,
        ]);
    }
}
