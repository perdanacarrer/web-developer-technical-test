<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_invalid_credentials_are_rejected()
    {
        $response = $this->post('/login', [
            'username' => 'wrong',
            'password' => 'wrong',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_valid_credentials_log_the_user_in_and_redirect_to_movies()
    {
        $response = $this->post('/login', [
            'username' => 'aldmic',
            'password' => '123abc123',
        ]);

        $response->assertRedirect('/movies');
    }

    public function test_movies_page_requires_login()
    {
        $response = $this->get('/movies');

        $response->assertRedirect('/login');
    }
}
