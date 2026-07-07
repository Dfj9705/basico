<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class EditProfile extends BaseEditProfile
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
                            ->relationship('grade', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('weapon_branch_id')
                            ->label('Arma')
                            ->relationship('weaponBranch', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('catalog_number')
                            ->label('No. de catálogo')
                            ->numeric()
                            ->required()
                            ->unique('users', 'catalog_number', ignoreRecord: true),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data'),
            ),
        ];
    }
}