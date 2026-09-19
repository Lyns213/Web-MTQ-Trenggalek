<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login as BaseAuth;

class LoginAdmin extends BaseAuth
{
    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}
