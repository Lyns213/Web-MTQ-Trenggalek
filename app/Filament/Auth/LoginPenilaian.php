<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
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
        $login = $data['login'];

        $name = match ($login) {
            'KTIQ' => ['KTIQ', 'MMQ'],
            'MKQ HIASAN MUSHAF' => ['MKQ HIASAN MUSHAF', 'MKQ Hiasan Mushaf', 'MKQ Hiasan'],
            'MKQ NASKAH' => ['MKQ NASKAH', 'MKQ Naskah'],
            'MKQ DEKORASI' => ['MKQ DEKORASI', 'MKQ Dekorasi'],
            'MKQ KONTEMPORER' => ['MKQ KONTEMPORER', 'MKQ Kontemporer'],
            'MsQ' => ['MsQ', 'MSQ'],
            'MFQ' => ['MFQ', 'MFQ '],
            'MHQ 1 Juz dan Tilawah' => ['MHQ 1 Juz dan Tilawah', 'MHQ 1 Juz'],
            'MHQ 5 Juz dan Tilawah' => ['MHQ 5 Juz dan Tilawah', 'MHQ 5 Juz'],
            'MHQ 10 Juz' => ['MHQ 10 Juz', 'MHQ 10 Juz dan Tilawah'],
            'MHQ 20 Juz' => ['MHQ 20 Juz', 'MHQ 20 Juz dan Tilawah'],
            'MHQ 30 Juz' => ['MHQ 30 Juz', 'MHQ 30 Juz dan Tilawah'],
            'Tilawah Anak-anak' => ['Tilawah Anak-anak', 'Tilawah Anak - anak', 'Tilawah Anak'],
            default => $login,
        };

        return [
            'name' => $name,
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $credentials = $this->getCredentialsFromFormData($data);

        $attempt = Filament::auth()->attempt($credentials, $data['remember'] ?? false);

        // Fallback for all branch password aliases (with or without numbers)
        if (! $attempt) {
            $passwordAlternatives = [
                'Tartil' => ['tartil1', 'tartil', 'Tartil1', 'Tartil'],
                'Tilawah Anak-anak' => ['anak2', 'anak', 'tilawahanak', 'tilawahanak2', 'Anak2', 'Anak'],
                'Tilawah Remaja' => ['remaja3', 'remaja', 'tilawahremaja', 'tilawahremaja3', 'Remaja3', 'Remaja'],
                'Tilawah Dewasa' => ['dewasa4', 'dewasa', 'tilawahdewasa', 'tilawahdewasa4', 'Dewasa4', 'Dewasa'],
                'MHQ 1 Juz dan Tilawah' => ['1juz5', '1juz', 'mhq1juz', 'mhq1juz5', 'satujuz', 'satujuz5', '1Juz5', '1Juz'],
                'MHQ 5 Juz dan Tilawah' => ['5juz6', '5juz', 'mhq5juz', 'mhq5juz6', 'limajuz', 'limajuz6', '5Juz6', '5Juz'],
                'MHQ 10 Juz' => ['10juz7', '10juz', 'mhq10juz', 'mhq10juz7', 'sepuluhjuz', 'sepuluhjuz7', '10Juz7', '10Juz'],
                'MHQ 20 Juz' => ['20juz8', '20juz', 'mhq20juz', 'mhq20juz8', 'duapuluhjuz', 'duapuluhjuz8', '20Juz8', '20Juz'],
                'MHQ 30 Juz' => ['30juz9', '30juz', 'mhq30juz', 'mhq30juz9', 'tigapuluhjuz', 'tigapuluhjuz9', '30Juz9', '30Juz'],
                'MFQ' => ['mfq10', 'mfq', 'Mfq10', 'Mfq', 'MFQ10', 'MFQ'],
                'MsQ' => ['msq11', 'msq', 'Msq11', 'Msq', 'MSQ11', 'MSQ'],
                'MKQ NASKAH' => ['naskah12', 'naskah', 'mkqnaskah', 'mkqnaskah12', 'Naskah12', 'Naskah'],
                'MKQ HIASAN MUSHAF' => ['hiasan13', 'hiasan', 'mkqhiasan', 'mkqhiasan13', 'Hiasan13', 'Hiasan'],
                'MKQ DEKORASI' => ['dekorasi14', 'dekorasi', 'mkqdekorasi', 'mkqdekorasi14', 'Dekorasi14', 'Dekorasi'],
                'MKQ KONTEMPORER' => ['kontemporer15', 'kontemporer', 'mkqkontemporer', 'mkqkontemporer15', 'Kontemporer15', 'Kontemporer'],
                'KTIQ' => ['mmq16', 'ktiq16', 'ktiq', 'mmq', 'Ktiq16', 'Ktiq', 'MMQ16', 'MMQ'],
            ];

            $branch = $data['login'];
            $entered = strtolower(trim($data['password']));
            $alts = $passwordAlternatives[$branch] ?? [];
            $lowerAlts = array_map('strtolower', $alts);

            if (in_array($entered, $lowerAlts, true)) {
                foreach ($alts as $alt) {
                    if ($alt === $data['password']) {
                        continue;
                    }
                    $credentials['password'] = $alt;
                    if (Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
                        $attempt = true;
                        break;
                    }
                }
            }
        }

        if (! $attempt) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
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
