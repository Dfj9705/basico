<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftAssignmentResource\Pages;
use App\Filament\Resources\ShiftAssignmentResource\RelationManagers;
use App\Models\ShiftAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftAssignmentResource extends Resource
{
    protected static ?string $model = ShiftAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Servicios';
    protected static ?string $navigationLabel = 'Asignación';

    // ...
    protected static ?string $title = 'Asignaciones';
    protected static ?string $modelLabel = 'Asignación';
    protected static ?string $pluralModelLabel = 'Asignaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->label('Usuario / Empleado'),
                Forms\Components\Select::make('shift_type_id')
                    ->relationship('shiftType', 'name')
                    ->required()
                    ->label('Tipo de Turno'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Fecha de Inicio'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Fecha de Fin (Opcional)'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas / Observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Usuario'),
                Tables\Columns\TextColumn::make('shiftType.name')
                    ->label('Turno'),
                Tables\Columns\TextColumn::make('shiftType.frequency')
                    ->badge()
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Desde'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('d/m/Y')
                    ->placeholder('Indefinido')
                    ->label('Hasta'),
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
            'index' => Pages\ListShiftAssignments::route('/'),
            'create' => Pages\CreateShiftAssignment::route('/create'),
            'edit' => Pages\EditShiftAssignment::route('/{record}/edit'),
        ];
    }
}
