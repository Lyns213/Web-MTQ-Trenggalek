<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;


class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component
    {
        return Select::make('login')
            ->label('Pilih Kecamatan')
            ->native(false)
            ->options([
                'Kecamatan Bendungan' => 'Kecamatan Bendungan',
                'Kecamatan Dongko' => 'Kecamatan Dongko',
                'Kecamatan Durenan' => 'Kecamatan Durenan',
                'Kecamatan Gandusari' => 'Kecamatan Gandusari',
                'Kecamatan Kampak' => 'Kecamatan Kampak',
                'Kecamatan Karangan' => 'Kecamatan Karangan',
                'Kecamatan Munjungan' => 'Kecamatan Munjungan',
                'Kecamatan Panggul' => 'Kecamatan Panggul',
                'Kecamatan Pogalan' => 'Kecamatan Pogalan',
                'Kecamatan Pule' => 'Kecamatan Pule',
                'Kecamatan Suruh' => 'Kecamatan Suruh',
                'Kecamatan Trenggalek' => 'Kecamatan Trenggalek',
                'Kecamatan Tugu' => 'Kecamatan Tugu',
                'Kecamatan Watulimo' => 'Kecamatan Watulimo',
            ])
            ->required();
            // ->autocomplete()
            // ->autofocus()
            // ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = 'name';

        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
