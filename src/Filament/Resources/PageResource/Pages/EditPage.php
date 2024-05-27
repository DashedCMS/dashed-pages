<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedCore\Filament\Concerns\HasEditableCMSActions;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Dashed\DashedPages\Models\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EditPage extends EditRecord
{
    use HasEditableCMSActions;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return self::CMSActions();
    }
}
