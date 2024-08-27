<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Dashed\DashedCore\Filament\Concerns\HasEditableCMSActions;

class EditPage extends EditRecord
{
    use HasEditableCMSActions;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return self::CMSActions();
    }
}
