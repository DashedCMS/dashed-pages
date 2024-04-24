<?php

namespace Dashed\DashedPages\Filament\Resources\PageResource\Pages;

use Dashed\DashedCore\Classes\Locales;
use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedCore\Filament\Actions\ShowSEOScoreAction;
use Dashed\DashedCore\Models\Redirect;
use Dashed\DashedPages\Filament\Resources\PageResource;
use Dashed\DashedPages\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class EditPage extends EditRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('view_page')
                ->button()
                ->label('Bekijk pagina')
                ->url($this->record->getUrl())
                ->openUrlInNewTab(),
            Action::make('Dupliceer pagina')
                ->action('duplicatePage')
                ->color('warning'),
            DeleteAction::make(),
            ShowSEOScoreAction::make(),
            LocaleSwitcher::make(),
        ];
    }

    public function duplicatePage()
    {
        $newPage = $this->record->replicate();
        foreach (Locales::getLocales() as $locale) {
            $newPage->setTranslation('slug', $locale['id'], $newPage->getTranslation('slug', $locale['id']));
            while (Page::where('slug->' . $locale['id'], $newPage->getTranslation('slug', $locale['id']))->count()) {
                $newPage->setTranslation('slug', $locale['id'], $newPage->getTranslation('slug', $locale['id']) . Str::random(1));
            }
        }

        $newPage->save();

        if ($this->record->customBlocks) {
            $newCustomBlock = $this->record->customBlocks->replicate();
            $newCustomBlock->blockable_id = $newPage->id;
            $newCustomBlock->save();
        }

        if ($this->record->metaData) {
            $newMetaData = $this->record->metaData->replicate();
            $newMetaData->metadatable_id = $newPage->id;
            $newMetaData->save();
        }

        return redirect(route('filament.dashed.resources.pages.edit', [$newPage]));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        while (Page::where('id', '!=', $this->record->id)->where('slug->' . $this->activeLocale, $data['slug'])->count()) {
            $data['slug'] .= Str::random(1);
        }

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];

        return $data;
    }

    public function beforeSave()
    {
        Redirect::handleSlugChangeForFilamentModel($this->record, $this->activeLocale, $this->data['slug']);
    }
}
