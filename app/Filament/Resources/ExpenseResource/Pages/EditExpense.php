<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\ExpenseSplit;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('split')
                ->label('Dividir')
                ->icon('heroicon-o-divide')
                ->requiresConfirmation()
                ->action(function ($record) {

                    ExpenseSplit::where('expense_id', $record->id)->delete();

                    $users = [];
                    if ($record->divisible == false) {
                        $users = User::join('weapon_branches', 'users.weapon_branch_id', '=', 'weapon_branches.id')
                            ->where('weapon_branches.force_id', $record->force_id)
                            ->select('users.id')
                            ->get();
                    } else {
                        $users = User::all();
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

                        $this->refreshFormData([
                            'splits',
                        ]);
                        $this->dispatch('refresh');
                    } else {
                        Notification::make()
                            ->title('No se puede dividir el gasto')
                            ->body('No se puede dividir el gasto porque no hay usuarios')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
