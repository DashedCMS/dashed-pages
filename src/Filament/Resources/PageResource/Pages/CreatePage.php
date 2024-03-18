<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Dashed\DashedPages\Models\Page;
<<<<<<< HEAD
use Filament\Actions\LocaleSwitcher;
=======
>>>>>>> 9ba0b63e5c9eb29bbb5f73a71e0a9ab509f521eb
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Illuminate\Support\Str;

class CreatePage extends CreateRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        while (Page::where('slug->' . $this->activeLocale, $data['slug'])->count()) {
            $data['slug'] .= Str::random(1);
        }

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];

        return $data;
    }
}
