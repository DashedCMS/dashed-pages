<?php

namespace Dashed\DashedPages;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Dashed\DashedPages\Filament\Resources\PageResource;

class DashedPagesPlugin implements Plugin
{
    public function getId(): string
    {
        return 'dashed-pages';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                PageResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {

    }
}
