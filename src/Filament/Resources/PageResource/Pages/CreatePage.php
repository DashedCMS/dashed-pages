<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Dashed\DashedCore\Filament\Concerns\HasCreatableCMSActions;

class CreatePage extends CreateRecord
{
    use HasCreatableCMSActions;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return self::CMSActions();
    }
}
