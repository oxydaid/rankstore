<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
// Jika ingin upload foto
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Nama
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                // Bagian Email
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                // Bagian Password (Wajib ada jika ingin fitur ganti password)
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
