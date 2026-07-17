<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pendaftaran mandiri sudah DITUTUP: seluruh akun dibuat oleh
     * administrator lewat Panel Admin. Halaman /register harus hilang.
     */
    public function test_halaman_pendaftaran_sudah_ditutup(): void
    {
        $this->get('/register')->assertNotFound();
    }
}
