<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Roles';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del rol')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\CheckboxList::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'name')
                    ->columns(2)
                    ->bulkToggleable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rol')
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Permisos')
                    ->badge()
                    ->limitList(3)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_roles');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_roles');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_roles');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_roles');
    }
}
