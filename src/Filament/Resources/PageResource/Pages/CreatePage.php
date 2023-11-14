<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Illuminate\Support\Str;
use Dashed\DashedPages\Models\Page;
use Dashed\DashedCore\Classes\Sites;
use Filament\Resources\Pages\CreateRecord;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePage extends CreateRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        while (Page::where('slug->' . $this->activeFormLocale, $data['slug'])->count()) {
            $data['slug'] .= Str::random(1);
        }

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];
        //        $content = $data['content'];
        //        $data['content'] = null;
        //        $data['content'][$this->activeFormLocale] = $content;

        return $data;
    }
}
