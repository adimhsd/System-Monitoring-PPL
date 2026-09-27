<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageCustomizationTest extends TestCase
{
    public function test_login_page_displays_uniku_logo_and_developer_footer_link(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('images/logo-uniku.png');
        $response->assertSee('Logo Universitas Kuningan');
        $response->assertSee('FEB - Universitas Kuningan');
        $response->assertSee('https://adi-muhamad.web.app/');
        $response->assertSee('Dosen Sontoloyo');
        $response->assertSee('wa.me/6285220621404');
    }

    public function test_forgot_password_page_displays_whatsapp_contact_button(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Hubungi Admin via WhatsApp');
        $response->assertSee('6285220621404');
    }
}
