<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPages extends ListRecords
{
    use Translatable;

    protected static string $resource = PageResource::class;
}
