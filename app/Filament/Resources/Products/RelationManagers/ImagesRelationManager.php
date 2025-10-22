<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Изображения';

    protected static ?string $modelLabel = 'изображение';

    protected static ?string $pluralModelLabel = 'изображения';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->maxLength(255)
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Снимка')
                    ->size(60)
                    ->square(),

                TextColumn::make('alt_text')
                    ->label('ALT текст')
                    ->limit(50)
                    ->placeholder('(автоматично генериран)'),

                CheckboxColumn::make('is_primary')
                    ->label('Основна')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Подредба')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Качена на')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('Добави снимка')
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
