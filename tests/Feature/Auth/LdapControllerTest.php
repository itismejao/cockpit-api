<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LdapControllerTest extends TestCase
{
    /**
     * Testa Inputs inválidos na autenticação por LDAP
     */
    public function testLoginValidateInput()
    {
        $response = $this->post('/api/auth/ldap/login');

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error' => [
                'uid',
                'password'
            ]
        ]);
    }

    /**
     * Testa credencias inválidas na autenticação por LDAP
     */
    public function testLoginInvalidCredentials()
    {
        $response = $this->post('/api/auth/ldap/login', ['uid' => 'adf', 'password' => '123123'], ['Accept' => 'application/json']);

        $response->assertStatus(401);
        $response->assertJsonStructure(['error']);
        $this->assertContains('Invalid credentials', $response->getContent());
    }

    /**
     * Testa login com sucesso
     */
    public function testLoginSucces()
    {
        $response = $this->post('/api/auth/ldap/login', ['uid' => env('LDAP_USER_UID'), 'password' => env('LDAP_USER_PASSWORD')], ['Accept' => 'application/json']);

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' =>
            ['uid', 'name', 'email', 'token']
        ]);
    }

}
