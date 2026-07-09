<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Gastos';
    protected static ?string $modelLabel = 'Gastos';
    protected static ?string $pluralModelLabel = 'Gastos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('expense_date')
                    ->default(now())
                    ->columnSpanFull()
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Toggle::make('divisible')
                    ->label('¿Es divisible por fuerza?')
                    ->required(),
                Forms\Components\Select::make('force_id')
                    ->relationship('force', 'name')
                    ->searchable()
                    ->preload()
                    ->default(auth()->user()->weaponBranch->force_id)
                    ->hidden(fn() => !auth()->user()->hasRole('Administrador')),

                Forms\Components\FileUpload::make('receipt')
                    ->label('Comprobante')
                    ->disk('public')
                    ->directory('expenses')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                Tables\Columns\IconColumn::make('divisible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('force.name')
                    ->label('Fuerza')
                    ->default('General')
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt')
                    ->label('Comprobante')
                    ->icon('heroicon-o-document-text')
                    ->formatStateUsing(fn(?string $state) => $state ? 'Ver archivo' : '-')
                    ->url(fn($record) => $record->receipt
                        ? Storage::disk('public')->url($record->receipt)
                        : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(!auth()->user()->hasRole('Administrador'), function ($query) {
                $query->where('force_id', auth()->user()->weaponBranch->force_id)
                    ->orWhereNull('force_id');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
