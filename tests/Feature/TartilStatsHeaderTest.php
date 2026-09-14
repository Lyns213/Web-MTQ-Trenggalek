<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Filament\Facades\Filament;

class TartilStatsHeaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('penilaian'));
    }

    public function test_tartil_page_renders_stats_header()
    {
        $user = User::where('email', 'tartil@penilaian.com')->first();
        $this->actingAs($user);

        $response = $this->get('/penilaian/nilai-tartils');
        $response->assertSuccessful();
        $response->assertSee('Total Pendaftar');
        $response->assertSee('Laki-Laki');
        $response->assertSee('Perempuan');
        $response->assertSee('Putra');
        $response->assertSee('Putri');
    }
}
