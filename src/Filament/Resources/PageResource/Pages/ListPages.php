<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Dashed\DashedPages\Filament\Resources\PageResource;
<<<<<<< HEAD
use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
=======
>>>>>>> 9ba0b63e5c9eb29bbb5f73a71e0a9ab509f521eb
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPages extends ListRecords
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
