<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Dashed\DashedCore\Filament\Concerns\HasCreatableCMSActions;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use HasCreatableCMSActions;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return self::CMSActions();
    }
}
