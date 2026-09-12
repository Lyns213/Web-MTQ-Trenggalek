<?php

namespace App\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $panel = Filament::getCurrentPanel();

        // Panel penilaian: selalu arahkan ke dashboard penilaian, abaikan
        // "intended" session yang basi (mis. / = panel admin -> 403 utk @penilaian.com)
        if ($panel && $panel->getId() === "penilaian") {
            return redirect()->to($panel->getUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}
