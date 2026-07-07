<?php

namespace App\Filament\Pages\Auth;

use App\Models\Grade;
use App\Models\WeaponBranch;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),

                        Select::make('grade_id')
                            ->label('Grado')
                            ->options(fn() => Grade::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('weapon_branch_id')
                            ->label('Arma')
                            ->options(fn() => WeaponBranch::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('catalog_number')
                            ->label('No. de catálogo')
                            ->numeric()
                            ->required()
                            ->unique('users', 'catalog_number'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}