<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
            ->modifyQueryUsing(fn ($query) => $query->with(['images' => fn ($q) => $q->where('is_primary', true)]))
            ->columns([
                ImageColumn::make('images.path')
                    ->label('Снимка')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder-product.svg'))
                    ->getStateUsing(function ($record) {
                        return $record->images->where('is_primary', true)->first()?->path;
                    })
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
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
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
