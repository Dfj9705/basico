<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Usuarios';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('grade_id')
                    ->label('Grado')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('weapon_branch_id')
                    ->label('Arma')
                    ->relationship('weaponBranch', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('catalog_number')
                    ->label('No. de catálogo')
                    ->numeric()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),

                Forms\Components\Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('weaponBranch.force.name')
                    ->label('Fuerza')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade.name')
                    ->label('Grado')
                    ->searchable(),

                Tables\Columns\TextColumn::make('weaponBranch.name')
                    ->label('Arma')
                    ->searchable(),

                Tables\Columns\TextColumn::make('catalog_number')
                    ->label('No. catálogo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('force_id')
                    ->label('Fuerza')
                    ->relationship('weaponBranch.force', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('weaponBranch_id')
                    ->label('Arma')
                    ->relationship('weaponBranch', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('grade_id')
                    ->label('Grado')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_users');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_users');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_users');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_users');
    }
}
