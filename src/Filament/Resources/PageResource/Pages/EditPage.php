<?php

namespace Qubiqx\QcommercePages\Filament\Resources\PageResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Support\Str;
use Qubiqx\QcommerceCore\Classes\Locales;
use Qubiqx\QcommerceCore\Classes\Sites;
use Qubiqx\QcommerceCore\Models\Redirect;
use Qubiqx\QcommercePages\Filament\Resources\PageResource;
use Qubiqx\QcommercePages\Models\Page;

class EditPage extends EditRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getActions(): array
    {
        return array_merge(parent::getActions(), [
            Action::make('view_page')
                ->button()
                ->label('Bekijk pagina')
                ->url($this->record->getUrl())
                ->openUrlInNewTab(),
            Action::make('Dupliceer pagina')
                ->action('duplicatePage')
                ->color('warning'),
            $this->getActiveFormLocaleSelectAction(),
        ]);
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

        return redirect(route('filament.resources.pages.edit', [$newPage]));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        while (Page::where('id', '!=', $this->record->id)->where('slug->' . $this->activeFormLocale, $data['slug'])->count()) {
            $data['slug'] .= Str::random(1);
        }

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];

        //        $content = $data['content'];
        //        $data['content'] = $this->record->content;
        //        $data['content'][$this->activeFormLocale] = $content;

        Redirect::handleSlugChange($this->record->getTranslation('slug', $this->activeFormLocale), $data['slug']);

        return $data;
    }
}
