<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Каталог';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section: Images
                Section::make('Изображения')
                    ->description('Качете снимки на продукта. Първата снимка автоматично става основна.')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Изображение')
                                    ->image()
                                    ->directory('products')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(5120) // 5MB
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('alt_text')
                                    ->label('ALT текст')
                                    ->placeholder('Автоматично генериран от името на продукта')
                                    ->helperText('За SEO и достъпност. Ако не попълните, ще се генерира автоматично.')
                                    ->columnSpanFull(),

                                Checkbox::make('is_primary')
                                    ->label('Основна снимка')
                                    ->helperText('Отметнете ако това е основната снимка на продукта')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label('Подредба')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('По-ниското число се показва първо'),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['alt_text'] ?? 'Изображение')
                            ->addActionLabel('+ Добави снимка')
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Section: Basic Information
                Section::make('Основна информация')
                    ->schema([
                        Select::make('category_id')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Име на продукта')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('SEO URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly версия на името'),

                        TextInput::make('sku')
                            ->label('SKU / Референция')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Уникален номер на продукта'),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(5)
                            ->columnSpanFull(),

                        TextInput::make('price')
                            ->label('Цена')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('лв.')
                            ->helperText('Цена с ДДС'),

                        TextInput::make('quantity')
                            ->label('Количество')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Налично количество'),

                        Toggle::make('active')
                            ->label('Активен')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Section: SEO
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Мета заглавие')
                            ->maxLength(60)
                            ->helperText('Оптимално: 50-60 символа'),

                        Textarea::make('meta_description')
                            ->label('Мета описание')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Оптимално: 150-160 символа')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primaryImage.path')
                    ->label('Снимка')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder-product.svg'))
                    ->size(50),

                TextColumn::make('name')
                    ->label('Име')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Категория')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('Референция')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU копиран!')
                    ->badge()
                    ->color('info'),

                TextColumn::make('price')
                    ->label('Цена')
                    ->money('BGN')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),

                IconColumn::make('active')
                    ->label('Статус')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
