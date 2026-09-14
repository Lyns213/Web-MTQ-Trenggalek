<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Filament\Facades\Filament;

class AllCabangStatsHeaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('penilaian'));
    }

    public static function urlsProvider(): array
    {
        return [
            ['tartil@penilaian.com', '/penilaian/nilai-tartils'],
            ['tilawahanak@penilaian.com', '/penilaian/nilai-anaks'],
            ['tilawahremaja@penilaian.com', '/penilaian/nilai-remajas'],
            ['tilawahdewasa@penilaian.com', '/penilaian/nilai-dewasas'],
            ['mhq1juzdantilawah@penilaian.com', '/penilaian/nilai-satu-juzs'],
            ['mhq5juzdantilawah@penilaian.com', '/penilaian/nilai-lima-juzs'],
            ['mhq10juz@penilaian.com', '/penilaian/nilai-sepuluh-juzs'],
            ['mhq20juz@penilaian.com', '/penilaian/nilai-duapuluh-juzs'],
            ['mhq30juz@penilaian.com', '/penilaian/nilai-tigapuluh-juzs'],
            ['mfq@penilaian.com', '/penilaian/nilai-mfqs'],
            ['msq@penilaian.com', '/penilaian/nilai-msqs'],
            ['mkqnaskah@penilaian.com', '/penilaian/nilai-naskahs'],
            ['mkqhiasan@penilaian.com', '/penilaian/nilai-mushafs'],
            ['mkqdekorasi@penilaian.com', '/penilaian/nilai-dekorasis'],
            ['mkqkontemporer@penilaian.com', '/penilaian/nilai-kontemporers'],
            ['mmq@penilaian.com', '/penilaian/nilai-mmqs'],
        ];
    }

    /**
     * @dataProvider urlsProvider
     */
    public function test_all_branches_render_stats_header(string $email, string $url)
    {
        $user = User::where('email', $email)->first();
        $this->actingAs($user);

        $response = $this->get($url);
        $response->assertSuccessful();
        $response->assertSee('Total Pendaftar');
        $response->assertSee('Laki-Laki');
        $response->assertSee('Perempuan');
    }
}
