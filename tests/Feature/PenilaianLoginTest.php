<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Filament\Facades\Filament;
use App\Filament\Auth\LoginPenilaian;

class PenilaianLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('penilaian'));
    }

    public static function branchCredentialsProvider(): array
    {
        return [
            ['Tartil', 'tartil'],
            ['Tartil', 'tartil1'],
            ['Tilawah Anak-anak', 'anak'],
            ['Tilawah Anak-anak', 'anak2'],
            ['Tilawah Remaja', 'remaja'],
            ['Tilawah Remaja', 'remaja3'],
            ['Tilawah Dewasa', 'dewasa'],
            ['Tilawah Dewasa', 'dewasa4'],
            ['MHQ 1 Juz dan Tilawah', '1juz'],
            ['MHQ 1 Juz dan Tilawah', '1juz5'],
            ['MHQ 5 Juz dan Tilawah', '5juz'],
            ['MHQ 5 Juz dan Tilawah', '5juz6'],
            ['MHQ 10 Juz', '10juz'],
            ['MHQ 10 Juz', '10juz7'],
            ['MHQ 20 Juz', '20juz'],
            ['MHQ 20 Juz', '20juz8'],
            ['MHQ 30 Juz', '30juz'],
            ['MHQ 30 Juz', '30juz9'],
            ['MFQ', 'mfq'],
            ['MFQ', 'mfq10'],
            ['MsQ', 'msq'],
            ['MsQ', 'msq11'],
            ['MKQ NASKAH', 'naskah'],
            ['MKQ NASKAH', 'naskah12'],
            ['MKQ HIASAN MUSHAF', 'hiasan'],
            ['MKQ HIASAN MUSHAF', 'hiasan13'],
            ['MKQ DEKORASI', 'dekorasi'],
            ['MKQ DEKORASI', 'dekorasi14'],
            ['MKQ KONTEMPORER', 'kontemporer'],
            ['MKQ KONTEMPORER', 'kontemporer15'],
            ['KTIQ', 'ktiq'],
            ['KTIQ', 'ktiq16'],
            ['KTIQ', 'mmq16'],
        ];
    }

    /**
     * @dataProvider branchCredentialsProvider
     */
    public function test_all_branches_can_login(string $branch, string $password)
    {
        Livewire::test(LoginPenilaian::class)
            ->fillForm([
                'login' => $branch,
                'password' => $password,
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/penilaian');

        $this->assertAuthenticated();
    }
}
