<?php

namespace Qubiqx\QcommercePages\Filament\Resources;

use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Qubiqx\QcommerceCore\Classes\Sites;
use Qubiqx\QcommerceCore\Filament\Concerns\HasCustomBlocksTab;
use Qubiqx\QcommerceCore\Filament\Concerns\HasMetadataTab;
use Qubiqx\QcommercePages\Filament\Resources\PageResource\Pages\CreatePage;
use Qubiqx\QcommercePages\Filament\Resources\PageResource\Pages\EditPage;
use Qubiqx\QcommercePages\Filament\Resources\PageResource\Pages\ListPages;
use Qubiqx\QcommercePages\Models\Page;

class PageResource extends Resource
{
    use Translatable;
    use HasMetadataTab;
    use HasCustomBlocksTab;

    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Pagina\'s';
    protected static ?string $label = 'Pagina';
    protected static ?string $pluralLabel = 'Pagina\'s';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'slug',
            'content',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 6,
                    '2xl' => 6,
                ])->schema([
                    Section::make('Content')
                        ->schema(array_merge([
                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->rules([
                                    'max:255',
                                ])
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state, $livewire) {
                                    if ($livewire instanceof CreatePage) {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->columnSpan([
                                    'default' => 2,
                                    'sm' => 2,
                                    'md' => 2,
                                    'lg' => 2,
                                    'xl' => 1,
                                    '2xl' => 1,
                                ]),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->unique('qcommerce__pages', 'slug', fn($record) => $record)
                                ->helperText('Laat leeg om automatisch te laten genereren')
                                ->required()
                                ->rules([
                                    'max:255',
                                ])
                                ->columnSpan([
                                    'default' => 2,
                                    'sm' => 2,
                                    'md' => 2,
                                    'lg' => 2,
                                    'xl' => 1,
                                    '2xl' => 1,
                                ]),
                            Builder::make('content')
                                ->blocks(cms()->builder('blocks'))
                                ->withBlockLabels()
                                ->columnSpan(2),
                        ], static::customBlocksTab(cms()->builder('pageBlocks'))))
                        ->columns([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 4,
                            '2xl' => 4,
                        ]),
                    Grid::make([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 2,
                        '2xl' => 2,
                    ])
                        ->schema([
                            Section::make('Globale informatie')
                                ->schema([
                                    DatePicker::make('start_date')
                                        ->label('Vul een startdatum in voor de pagina:')
                                        ->helperText('Indien je geen startdatum opgeeft, is de pagina direct zichtbaar')
                                        ->rules([
                                            'nullable',
                                            'date',
                                        ]),
                                    DatePicker::make('end_date')
                                        ->label('Vul een einddatum in voor de pagina:')
                                        ->helperText('Indien je geen einddatum opgeeft, vervalt de pagina niet')
                                        ->rules([
                                            'nullable',
                                            'date',
                                            'after:startDate',
                                        ]),
                                    Toggle::make('is_home')
                                        ->label('Dit is de homepagina'),
                                    Select::make('parent_page_id')
                                        ->relationship('parentPage', 'name')
                                        ->options(fn($record) => Page::where('id', '!=', $record->id ?? 0)->pluck('name', 'id'))
                                        ->label('Bovenliggende pagina'),
                                    Select::make('site_id')
                                        ->label('Actief op site')
                                        ->options(collect(Sites::getSites())->pluck('name', 'id'))
                                        ->hidden(function () {
                                            return !(Sites::getAmountOfSites() > 1);
                                        })
                                        ->required(),
                                ])
                                ->columnSpan([
                                    'default' => 1,
                                    'sm' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                    'xl' => 2,
                                    '2xl' => 2,
                                ]),
                            Section::make('Meta data')
                                ->schema(static::metadataTab())
                                ->columnSpan([
                                    'default' => 1,
                                    'sm' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                    'xl' => 2,
                                    '2xl' => 2,
                                ]),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->sortable()
                    ->searchable([
                        'name',
                        'slug',
                        'content',
                        'meta_title',
                        'meta_description',
                    ]),
                TextColumn::make('site_id')
                    ->label('Actief op site')
                    ->sortable()
                    ->hidden(!(Sites::getAmountOfSites() > 1))
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn($record) => ucfirst($record->status)),
            ])
            ->filters([
                //
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
