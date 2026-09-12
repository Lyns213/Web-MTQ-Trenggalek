<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;

class LoginPenilaian extends BaseAuth
{
    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

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
            ->label('Pilih Cabang Musabaqah')
            ->placeholder('Pilih Cabang Penilaian...')
            ->native(false)
            ->extraInputAttributes([
                'style' => 'color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; font-weight: 800 !important; font-size: 15px !important;',
            ])
            ->options([
                'Tartil' => 'Tartil',
                'Tilawah Anak-anak' => 'Tilawah Anak-anak',
                'Tilawah Remaja' => 'Tilawah Remaja',
                'Tilawah Dewasa' => 'Tilawah Dewasa',
                'MHQ 1 Juz dan Tilawah' => 'MHQ 1 Juz dan Tilawah',
                'MHQ 5 Juz dan Tilawah' => 'MHQ 5 Juz dan Tilawah',
                'MHQ 10 Juz' => 'MHQ 10 Juz',
                'MHQ 20 Juz' => 'MHQ 20 Juz',
                'MHQ 30 Juz' => 'MHQ 30 Juz',
                'MFQ' => 'MFQ',
                'MsQ' => 'MsQ',
                'MKQ NASKAH' => 'MKQ NASKAH',
                'MKQ HIASAN MUSHAF' => 'MKQ HIASAN MUSHAF',
                'MKQ DEKORASI' => 'MKQ DEKORASI',
                'MKQ KONTEMPORER' => 'MKQ KONTEMPORER',
                'KTIQ' => 'KTIQ',
            ])
            ->required();
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

    protected function getRedirectUrl(): string
    {
        return '/penilaian';
    }
}
