<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Faker\Provider\Base;
use Faker\Provider\DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function body () {
        return
            $body = [
            'name' => 'UserPOPO',
            'password' => 'password',
            'password_confirmation' => 'password',
            'telephone' => '089898978',
            'address' => '15 rue du Chatillon 92345',
            'additional_address' => '909090',
            'postal_code' => '92345',
            'ville' => 'Antony',
             'canLegalyPlay' => 1,
             'newsletter' => 1,
            'email'=> 'admin9000@gmail.com',
            'email_verified_at'=> Carbon::now()
        ];
    }

    public function bodySocialite() {
        return
            $body = [
                'id' => '12345678904587',
                'name' => 'Usergoogle',
                'email'=> 'adminsocial9000@gmail.com',
                'provider' => 'GOOGLE',
                'canLegalyPlay' => 1
            ];
    }


    public function testValidRegisterWithSocialite() {
        $body = $this->bodySocialite();

       $user =  User::where('email', $body['email'])->first();

        if($user and !empty($user->google_id))
        {
            $response = $this->json('POST', '/api/register_with_socialite', $body, ['Accept' => 'application/json'])
                ->assertStatus(401);
        }else  {
            $response = $this->json('POST', '/api/register_with_socialite', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['user', 'token']);
        }
    }


    public function testInValidRegisterWithSocialite() {
        $body = [
            'id' => '12345678904587',
            'name' => 'Usergoogle',
            'email'=> 'admi0@.com',
            'provider' => 'GOOGLE',
            'canLegalyPlay' => 1
        ];;

        $user =  User::where('email', $body['email'])->first();

        $response = $this->json('POST', '/api/register_with_socialite', $body, ['Accept' => 'application/json'])
            ->assertStatus(402);
    }


    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testApiInvalidRegister() {
        $body = [
            'name' => 'UserPOPO',
            'password' => 'password',
            'password_confirmation' => 'password',
            'telephone' => '089898978',
        ];
        $this->json('POST','/api/register',$body,['Accept' => 'application/json'])
            ->assertStatus(400);
    }



    public function testApiInvalidRegisterWithInvalidCanLegalyPlay() {
        $body = [
            'id' => '12345678904587',
            'name' => 'Usergoogle',
            'email'=> 'adminsocialLegalyPlay9000@gmail.com',
            'provider' => 'GOOGLE',
            'canLegalyPlay' => 0
        ];
        $this->json('POST','/api/register_with_socialite',$body,['Accept' => 'application/json'])
            ->assertStatus(401);
    }

    /*
    public function testApiRegister() {
        $body = $this->body();

        $this->json('POST','/api/register',$body,['Accept' => 'application/json'])
            ->assertStatus(201);
    }
    */

    public function testUpdateInformationInvalidUser() {

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {

            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);

           // $user->email_verified_at  = Carbon::now();
           // $user->save();
        }

        $body_user = [
            'telephone' =>'0789090912',
            'addresse' => '12 avenue du Resolvet'
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/user/update/00', $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/user/update/1999', $body_user);
        }

        $responseo->assertStatus(404);


    }



    public function testUpdateInformationValidUser() {

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $user ->email_verified_at = Carbon::now();
            $user->save();
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }

        $body_user = [
            'telephone' =>'0789090912',
            'addresse' => '12 avenue du Resolvet'
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/user/update/'.$user->id, $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/user/update/'.$user->id, $body_user);
        }

        $responseo->assertStatus(200);


    }





       public function testLogout() {

           $body = $this->body();

           $user = User::where('email', 'admin9000@gmail.com')->first();

           if ($user) {
               $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                   ->assertStatus(200)
                   ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
           } else {
               $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                   ->assertStatus(201)
                   ->assertJsonStructure(['message', 'user', 'token']);
           }


           if($user) {
               $responseo = $this->withHeaders([
                   'Authorization' => 'Bearer ' . $response["access_token"],
                   'Accept' => 'application/json'
               ])->post('api/logout');
           }else  {

               $responseo = $this->withHeaders([
                   'Authorization' => 'Bearer ' . $response['token'],
                   'Accept' => 'application/json'
               ])->post(' api/logout');
           }

           $responseo->assertStatus(200);


       }



    public function testUserProfile() {

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->get('api/user-profile');
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->get('api/user-profile');
        }

        $responseo->assertStatus(200);


    }



    public function testGetAllReclamation() {

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->get('api/user/all_reclamation');
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->get('api/user/all_reclamation');
        }

        $responseo->assertStatus(200);


    }



    public function testGetAllReclamationInvalidUser() {

        $responseo = $this->get('api/user/all_reclamation');

        $responseo->assertStatus(400);
    }

    public function testGetAllHistoricalInvalidUser() {

        $responseo = $this->get('api/user/all_historical');

        $responseo->assertStatus(400);
    }


    public function testGetAllHistorical() {

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->get('api/user/all_historical');
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->get('api/user/all_historical');
        }

        $responseo->assertStatus(200);


    }


    public function testUpdatePasswordWithInvalidInput() {

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        $body_user = [
            'old_password' =>'',
            'new_password' => '12 avenue du Resolvet',
            'new_password_confirmation' => '12 avenue du Resolvet'
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/update_password/', $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/update_password/', $body_user);
        }

        $responseo->assertStatus(422);


    }


    public function testUpdatePasswordWithInvlaidOldPasssword() {
        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        $body_user = [
            'old_password' =>'secret12345',
            'new_password' => '12 avenue du Resolvet',
            'new_password_confirmation' => '12 avenue du Resolvet'
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/update_password/', $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/update_password/', $body_user);
        }

        $responseo->assertStatus(400);

    }



    public function testUpdatePasswordWithValidInput() {
        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        $body_user = [
            'old_password' =>'password',
            'new_password' => 'password',
            'new_password_confirmation' => 'password'
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/update_password/', $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/update_password/', $body_user);
        }

        $responseo->assertStatus(200);

    }


    public function testForgetPasswordWithoutEmail() {


        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        $body_user = [
            'email' =>''
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/forget_password/', $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/forget_password/', $body_user);
        }

        $responseo->assertStatus(422);

    }


    public function testForgetPasswordWithEmailNotFound() {


        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        $body_user = [
            'email' =>'dmin9000@gmail.com'
        ];

        if($user) {
            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/forget_password/', $body_user);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/forget_password/', $body_user);
        }

        $responseo->assertStatus(404);

    }

    /*
    public function testForgetPasswordWithValidEmail() {


        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {
            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }


        $body_user = [
            'email' =>'admin9000@gmail.com'
        ];

        $body_password = [
            'old_password' =>'password',
            'new_password' => 'password',
            'new_password_confirmation' => 'password'
        ];

        if($user) {
            $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/update_password/', $body_password);

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('api/forget_password/', $body_user);




        }else  {

            $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/update_password/', $body_password);

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post(' api/forget_password/', $body_user);


        }

        $responseo->assertStatus(200);

    }
    */



}
