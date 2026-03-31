<?php

namespace Dashed\DashedPages\Filament\Resources;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Dashed\DashedPages\Models\Page;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Dashed\DashedCore\Classes\QueryHelpers\SearchQuery;
use Dashed\DashedCore\Filament\Concerns\HasVisitableTab;
use Dashed\DashedCore\Filament\Concerns\HasCustomBlocksTab;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Dashed\DashedCore\Classes\Actions\ActionGroups\ToolbarActions;
use Dashed\DashedPages\Filament\Resources\PageResource\Pages\EditPage;
use Dashed\DashedPages\Filament\Resources\PageResource\Pages\ListPages;
use Dashed\DashedPages\Filament\Resources\PageResource\Pages\CreatePage;

class PageResource extends Resource
{
    use Translatable;
    use HasVisitableTab;
    use HasCustomBlocksTab;

    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static string | UnitEnum | null $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Pagina\'s';
    protected static ?string $label = 'Pagina';
    protected static ?string $pluralLabel = 'Pagina\'s';
    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'slug',
            'content',
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Content')->columnSpanFull()
                    ->schema(array_merge([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->lazy()
                            ->afterStateUpdated(function (Set $set, $state, $livewire) {
                                if ($livewire instanceof CreatePage) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->unique('dashed__pages', 'slug', fn ($record) => $record)
                            ->helperText('Laat leeg om automatisch te laten genereren')
                            ->maxLength(255),
                        cms()->getFilamentBuilderBlock(),
                    ], static::customBlocksTab('pageBlocks')))
                    ->columns(2),
                Section::make('Globale informatie')
                    ->columnSpanFull()
                    ->schema(array_merge(
                        [
                            Toggle::make('is_home')
                                ->label('Dit is de homepagina'),
                        ],
                        static::publishTab()
                    )),
                Section::make('Meta data')
                    ->columnSpanFull()
                    ->schema(static::metadataTab()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(array_merge([
                TextColumn::make('name')
                    ->label('Naam')
                    ->sortable()
                    ->searchable(query: SearchQuery::make()),
            ], static::visitableTableColumns()))
            ->filters([
                TrashedFilter::make(),
            ])
            ->toolbarActions(ToolbarActions::getActions())
            ->recordActions([
                EditAction::make()
                    ->button(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
