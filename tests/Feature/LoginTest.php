<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
     public function testApiLogin() {
        $body = [
            'username' => 'admin@admin.com',
            'password' => 'admin'
        ];
        $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
            ->assertStatus(422);
    }

    public function testApiInvalidParameterLogin() {
            $body = [
                'username' => null,
                'password' => null
            ];
            $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
                ->assertStatus(422);
    }


}
