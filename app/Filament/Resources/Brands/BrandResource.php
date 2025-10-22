<?php

declare(strict_types=1);

namespace App\Filament\Resources\Brands;

use App\Filament\Resources\Brands\Pages\ManageBrands;
use App\Models\Brand;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationGroup = 'Каталог';

    protected static ?int $navigationSort = 4;

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
                Section::make('Основна информация')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Име на марката')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        TextInput::make('slug')
                            ->label('SEO URL')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Автоматично се генерира от името'),

                        FileUpload::make('logo')
                            ->label('Лого')
                            ->image()
                            ->directory('brands'),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Мета заглавие')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Мета описание')
                            ->rows(3),
                    ]),

                Section::make('Настройки')
                    ->columns(1)
                    ->schema([
                        Toggle::make('active')
                            ->label('Активна')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('logo')
                    ->label('Лого')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Име')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('SEO URL')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('products_count')
                    ->label('Брой продукти')
                    ->counts('products')
                    ->sortable(),

                ToggleColumn::make('active')
                    ->label('Активна')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Създадена на')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                TernaryFilter::make('active')
                    ->label('Активна')
                    ->placeholder('Всички марки')
                    ->trueLabel('Само активни')
                    ->falseLabel('Само неактивни'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBrands::route('/'),
        ];
    }
}
