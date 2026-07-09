<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaponBranchResource\Pages;
use App\Filament\Resources\WeaponBranchResource\RelationManagers;
use App\Models\WeaponBranch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WeaponBranchResource extends Resource
{
    protected static ?string $model = WeaponBranch::class;
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Armas';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('force_id')
                    ->label('Fuerza')
                    ->relationship('force', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('force.name')
                    ->label('Fuerza')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListWeaponBranches::route('/'),
            'create' => Pages\CreateWeaponBranch::route('/create'),
            'edit' => Pages\EditWeaponBranch::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_weapon_branches');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_weapon_branches');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_weapon_branches');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_weapon_branches');
    }
}
