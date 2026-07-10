<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\CashBoxMovement;
use App\Models\ExpenseSplit;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn($record) => !$record->splits()->whereHas('movements', fn($q) => $q->where('type', 'transferencia'))->exists()),
            Actions\Action::make('split')
                ->label('Dividir')
                ->icon('heroicon-o-divide')
                ->requiresConfirmation()
                ->visible(fn($record) => !$record->splits()->whereHas('movements', fn($q) => $q->where('type', 'transferencia'))->exists())
                ->action(function ($record) {

                    ExpenseSplit::where('expense_id', $record->id)->delete();

                    $users = [];
                    if ($record->divisible == false) {
                        $users = User::join('weapon_branches', 'users.weapon_branch_id', '=', 'weapon_branches.id')
                            ->where('weapon_branches.force_id', $record->force_id)
                            ->select('users.id')
                            ->get();
                    } else {
                        $users = User::all()->pluck('id');
                    }

                    if (count($users) >= 1) {
                        foreach ($users as $key => $user) {
                            ExpenseSplit::create([
                                'expense_id' => $record->id,
                                'user_id' => $user->id,
                                'amount' => $record->amount / count($users),
                                'description' => $record->description,
                            ]);
                        }

                        Notification::make()
                            ->title('Gasto dividido')
                            ->body('El gasto se ha dividido correctamente')
                            ->success()
                            ->send();

                        $this->redirect(
                            ExpenseResource::getUrl('edit', ['record' => $record])
                        );
                    } else {
                        Notification::make()
                            ->title('No se puede dividir el gasto')
                            ->body('No se puede dividir el gasto porque no hay usuarios')
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('payment_receipt')
                ->label('Recibo de pago')
                ->icon('heroicon-o-receipt-refund')
                ->form([
                    FileUpload::make('payment_receipt')
                        ->label('Recibo/Comprobante')
                        ->directory('expenses/payment_receipts')
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'application/pdf'])
                        ->visibility('public')
                        ->disk('public')
                        ->required(),
                ])
                ->visible(fn($record) => $record->payment_receipt == null)
                ->action(function ($record, $form) {

                    $balance = CashBoxMovement::getTotalByForce(auth()->user()->weaponBranch->force_id);

                    if ($balance < $record->amount) {
                        Notification::make()
                            ->title('Saldo insuficiente')
                            ->body('No se puede transferir el gasto porque no hay saldo suficiente')
                            ->danger()
                            ->send();
                        return;
                    }
                    $record->update($form->getState());
                    CashBoxMovement::create([
                        'force_id' => auth()->user()->weaponBranch->force_id,
                        'user_id' => $record['user_id'],
                        'expense_id' => $record['id'],
                        'quantity' => $record['amount'],
                        'type' => 'egreso',
                        'observation' => 'Pago de gasto: ' . $record['description'],
                    ]);

                    Notification::make()
                        ->title('Recibo de pago guardado')
                        ->body('El recibo de pago se ha guardado correctamente')
                        ->success()
                        ->send();

                    $this->redirect(
                        ExpenseResource::getUrl('edit', ['record' => $record])
                    );
                }),
        ];
    }
}
