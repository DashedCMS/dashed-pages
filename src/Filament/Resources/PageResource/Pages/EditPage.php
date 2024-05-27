<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Dashed\DashedCore\Filament\Concerns\HasEditableCMSActions;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HasEditableCMSActions;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return self::CMSActions();
    }
}
