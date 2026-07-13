<?php

namespace App\Filament\Resources\ExpenseResource\RelationManagers;

use App\Models\CashBoxMovement;
use App\Models\ExpenseSplit;
use App\Models\Force;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SplitsRelationManager extends RelationManager
{
    protected static string $relationship = 'splits';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('splits')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('splits')
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('transferencia')
                    ->label('Transferencia')
                    ->badge()
                    ->state(function (ExpenseSplit $record) {
                        return $record->movements()
                            ->where('type', 'transferencia')
                            ->exists()
                            ? 'Transferido'
                            : 'Pendiente';
                    })
                    ->color(function (ExpenseSplit $record) {
                        return $record->movements()
                            ->where('type', 'transferencia')
                            ->exists()
                            ? 'success'
                            : 'danger';
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                // Tables\Actions\DeleteBulkAction::make(),
                // ]),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\BulkAction::make('Transferir')
                        ->label('Transferir')
                        ->icon('heroicon-o-arrow-path')
                        ->fillForm(function (Collection $records): array {
                            return [
                                'amount' => $records->sum('amount'),
                            ];
                        })
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Monto')
                                ->numeric()
                                ->readOnly(),

                            Forms\Components\Select::make('force_id')
                                ->label('Fuerza')
                                ->options(
                                    Force::where('is_active', true)->get()->pluck('name', 'id')
                                )
                                ->searchable()
                                ->preload()
                                ->default(!$this->getOwnerRecord()->divisible ? auth()->user()->weaponBranch->force_id : null)
                                ->required(),

                            Forms\Components\Textarea::make('note')
                                ->label('Observación')
                                ->required()
                                ->default('Transferencia del gasto ' . $this->getOwnerRecord()->description . ' de ' . auth()->user()->weaponBranch->force->name),
                        ])
                        ->action(function (array $data, $records, $livewire) {
                            $balance = CashBoxMovement::getTotalByForce(auth()->user()->weaponBranch->force_id);

                            if ($balance < $records->sum('amount')) {
                                Notification::make()
                                    ->title('Saldo insuficiente')
                                    ->body('No se puede transferir el gasto porque no hay saldo suficiente')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            foreach ($records as $key => $record) {
                                CashBoxMovement::create([
                                    'force_id' => $data['force_id'],
                                    'user_id' => $record->user_id,
                                    'expense_split_id' => $record->id,
                                    'quantity' => $record['amount'],
                                    'type' => 'transferencia',
                                    'observation' => $data['note'],
                                ]);


                                CashBoxMovement::create([
                                    'force_id' => auth()->user()->weaponBranch->force_id,
                                    'user_id' => $record->user_id,
                                    'expense_split_id' => $record->id,
                                    'quantity' => $record['amount'],
                                    'type' => 'egreso',
                                    'observation' => $data['note'],
                                ]);
                            }

                            Notification::make()
                                ->title('Gasto transferido')
                                ->body('Las cuotas del gasto se han transferido correctamente')
                                ->success()
                                ->send();
                        })
                    ,
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(function ($record): bool {
                if (auth()->user()->hasRole('Administrador')) {
                    return true;
                }

                if ($record->movements()->where('type', 'transferencia')->exists()) {
                    return false;
                }

                return $record->user->weaponBranch->force_id
                    === auth()->user()->weaponBranch->force_id;
            });
    }


}
