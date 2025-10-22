<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses;

use App\Filament\Resources\Warehouses\Pages\ManageWarehouses;
use App\Models\Warehouse;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'Каталог';

    protected static ?int $navigationSort = 3;

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
                            ->label('Име на склада')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('image')
                            ->label('Снимка')
                            ->image()
                            ->directory('warehouses'),

                        Textarea::make('address')
                            ->label('Адрес')
                            ->rows(3),

                        Textarea::make('working_hours')
                            ->label('Работно време')
                            ->rows(3)
                            ->placeholder('Пон-Пет: 9:00-18:00\nСъб: 10:00-14:00\nНед: Почивен ден'),
                    ]),

                Section::make('Контакти')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('url')
                            ->label('URL за повече информация')
                            ->url()
                            ->maxLength(255),
                    ]),

                Section::make('Настройки')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_physical_store')
                            ->label('Физически магазин')
                            ->helperText('Ако е маркиран, складът се показва като опция "Вземи от магазин"')
                            ->default(false),

                        Toggle::make('active')
                            ->label('Активен')
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

                ImageColumn::make('image')
                    ->label('Снимка')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Име')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),

                IconColumn::make('is_physical_store')
                    ->label('Физически магазин')
                    ->boolean()
                    ->sortable(),

                ToggleColumn::make('active')
                    ->label('Активен')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Създаден на')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                TernaryFilter::make('active')
                    ->label('Активен')
                    ->placeholder('Всички складове')
                    ->trueLabel('Само активни')
                    ->falseLabel('Само неактивни'),

                TernaryFilter::make('is_physical_store')
                    ->label('Физически магазин')
                    ->placeholder('Всички складове')
                    ->trueLabel('Само физически магазини')
                    ->falseLabel('Само складове'),
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
            'index' => ManageWarehouses::route('/'),
        ];
    }
}
