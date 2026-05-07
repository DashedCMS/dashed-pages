<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Dashed\DashedPages\Filament\Resources\PageResource;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Dashed\DashedCore\Filament\Concerns\HasNestableSortingAction;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPages extends ListRecords
{
    use HasNestableSortingAction;
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter([
            $this->getNestableSortingHeaderAction(),
            CreateAction::make(),
            LocaleSwitcher::make(),
        ]));
    }
}
