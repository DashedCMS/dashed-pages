<?php

namespace Dashed\DashedPages;

use Dashed\DashedPages\Filament\Resources\PageResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

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
