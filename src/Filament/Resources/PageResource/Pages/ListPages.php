<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Dashed\DashedPages\Filament\Resources\PageResource;

class ListPages extends ListRecords
{
    use Translatable;

    protected static string $resource = PageResource::class;
}
