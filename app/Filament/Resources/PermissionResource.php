<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Permisos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del permiso')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Permiso')
                    ->searchable(),

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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_permissions');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_permissions');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_permissions');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_permissions');
    }
}
