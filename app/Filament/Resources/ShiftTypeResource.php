<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftTypeResource\Pages;
use App\Filament\Resources\ShiftTypeResource\RelationManagers;
use App\Models\ShiftType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftTypeResource extends Resource
{
    protected static ?string $model = ShiftType::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Servicios';
    protected static ?string $navigationLabel = 'Tipos de Servicios';

    // ...
    protected static ?string $title = 'Tipos de Servicios';
    protected static ?string $modelLabel = 'Tipo de Servicio';
    protected static ?string $pluralModelLabel = 'Tipos de Servicios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nombre del Turno'),
                Forms\Components\Select::make('frequency')
                    ->options([
                        'daily' => 'Diario',
                        'weekly' => 'Semanal',
                    ])
                    ->required()
                    ->label('Frecuencia'),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Hora de Inicio'),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Hora de Fin'),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->label('Nombre'),
                Tables\Columns\TextColumn::make('frequency')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'daily' => 'success',
                        'weekly' => 'warning',
                    })
                    ->label('Frecuencia'),
                Tables\Columns\TextColumn::make('start_time')->time('H:i')->label('Inicio'),
                Tables\Columns\TextColumn::make('end_time')->time('H:i')->label('Fin'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListShiftTypes::route('/'),
            'create' => Pages\CreateShiftType::route('/create'),
            'edit' => Pages\EditShiftType::route('/{record}/edit'),
        ];
    }
}
