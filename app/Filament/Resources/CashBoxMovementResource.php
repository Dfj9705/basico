<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashBoxMovementResource\Pages;
use App\Models\CashBoxMovement;
use App\Models\Force;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CashBoxMovementResource extends Resource
{
    protected static ?string $model = CashBoxMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Caja';

    protected static ?string $modelLabel = 'Movimiento de caja';

    protected static ?string $pluralModelLabel = 'Movimientos de caja';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('force_id')
                ->label('Fuerza')
                ->relationship('force', 'name')
                ->required()
                ->disabled(),

            Forms\Components\Select::make('type')
                ->label('Tipo')
                ->options([
                    'ingreso' => 'Ingreso',
                    'egreso' => 'Egreso',
                    'transferencia' => 'Transferencia recibida',
                ])
                ->required()
                ->disabled(),

            Forms\Components\TextInput::make('quantity')
                ->label('Monto')
                ->prefix('Q')
                ->numeric()
                ->disabled(),

            Forms\Components\Select::make('user_id')
                ->label('Usuario relacionado')
                ->relationship('user', 'name')
                ->disabled(),


            Forms\Components\Textarea::make('observation')
                ->label('Descripción')
                ->columnSpanFull()
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('force.name')
                    ->label('Fuerza')
                    ->badge()
                    ->sortable()
                    ->visible(fn(): bool => auth()->user()->hasRole('Administrador')),

                Tables\Columns\TextColumn::make('type')
                    ->label('Movimiento')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'ingreso' => 'Ingreso',
                        'egreso' => 'Egreso',
                        'transferencia' => 'Transferencia recibida',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'ingreso' => 'success',
                        'transferencia' => 'info',
                        'egreso' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Monto')
                    ->money('GTQ')
                    ->weight('bold')
                    ->color(
                        fn(CashBoxMovement $record): string =>
                        $record->type === 'egreso' ? 'danger' : 'success'
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario relacionado')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('registeredBy.name')
                    ->label('Registrado por')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('observation')
                    ->label('Descripción')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Origen')
                    ->state(function (CashBoxMovement $record): string {
                        if ($record->contribution_id) {
                            return 'Contribución #' . $record->contribution_id;
                        }

                        if ($record->expense_id) {
                            return 'Gasto #' . $record->expense_id;
                        }

                        if ($record->expense_split_id) {
                            return 'División de gasto #' . $record->expense_split_id;
                        }

                        return 'Movimiento manual';
                    })
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('force_id')
                    ->label('Fuerza')
                    ->options(fn() => Force::query()
                        ->where('is_active', true)
                        ->pluck('name', 'id'))
                    ->visible(fn(): bool => auth()->user()->hasRole('Administrador')),

                SelectFilter::make('type')
                    ->label('Tipo de movimiento')
                    ->options([
                        'ingreso' => 'Ingreso',
                        'egreso' => 'Egreso',
                        'transferencia' => 'Transferencia recibida',
                    ]),


                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),

                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn(Builder $query, $date) =>
                                $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date) =>
                                $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if (!$user->hasRole('Administrador')) {
            $query->where(
                'force_id',
                $user->weaponBranch?->force_id
            );
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_cash_box_movements');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashBoxMovements::route('/'),
            'view' => Pages\ViewCashBoxMovement::route('/{record}'),
        ];
    }
}