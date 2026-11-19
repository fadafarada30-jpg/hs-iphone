<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_new_hero_and_cta_buttons(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Upgrade gadget')
            ->assertSee('tanpa was-was')
            ->assertSee('100% lolos QC & garansi sinyal', false)
            ->assertSee('Lihat Stok Ready')
            ->assertSee('Jual / Tukar Tambah HP');
    }

    public function test_homepage_shows_trust_sections(): void
    {
        $this->get(route('home'))
            ->assertSee('Kenapa beli di HS Depok?')
            ->assertSee('30-Point Quality Control')
            ->assertSee('Garansi Tukar Unit 7 Hari')
            ->assertSee('Bisa COD / Cek Langsung')
            ->assertSee('Kata pembeli, buktinya di sini.')
            ->assertSee('Mampir, cek unitnya langsung.');
    }

    public function test_about_page_renders(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Kami cek dulu')
            ->assertSee('Perjalanan usaha kami')
            ->assertSee('3uTools')
            ->assertSee('Kenalan dengan tim kami');
    }

    public function test_grading_page_renders(): void
    {
        $this->get(route('grading'))
            ->assertOk()
            ->assertSee('Grade A+')
            ->assertSee('Grade A')
            ->assertSee('Grade B')
            ->assertSee('Garansi sinyal');
    }
}
