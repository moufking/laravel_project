<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchLotTest extends TestCase
{

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

    public function testApiNotAuthorizedToRequest()
    {
        $body = [
            'numberTicket' => '10909090',
        ];

        $this->json('POST', '/api/search-lot', $body, ['Accept' => 'application/json'])
            ->assertStatus(400);
    }

    public function testApiAuthorizedToRequestWithInvalidNumber()
    {

        $body_seachLot = [
            'numberTicket' => '',
        ];

        $body = $this->body();

        $user = User::where('email', 'admin9000@gmail.com')->first();

        if ($user) {

            $user->email_verified_at  = Carbon::now();
            $user->save();
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {

            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);
        }

        if($user) {
            $user->email_verified_at  = Carbon::now();
            $user->save();

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('/api/search-lot', $body_seachLot);
        }else  {

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response['token'],
                'Accept' => 'application/json'
            ])->post('/api/search-lot', $body_seachLot);

           // $user->email_verified_at  = Carbon::now();
            //$user->save();
        }

        $responseo->assertStatus(401);


    }

    public function testApiAuthorizedToRequestWithValidNumberNotFoundDb()
    {

        $body_seachLot = [
            'numberTicket' => '1090909000',
        ];

        $body = $this->body();

        $user = User::where('email', $body['email'])->first();

        if ($user) {
            $user->email_verified_at  = Carbon::now();
            $user->save();
            $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
        } else {

            $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message', 'user', 'token']);

            $user->email_verified_at  = Carbon::now();
            $user->save();


        }

        $responseo = $this->withHeaders([
            'Authorization' => 'Bearer ' . $response["access_token"],
            'Accept' => 'application/json'
        ])->post('/api/search-lot', $body_seachLot);

        $responseo->assertStatus(400);


    }

    public function testApiAuthorizedToRequestWithValidNumber()
    {
        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)
            ->where('endDate','>=',Carbon::now())
            ->where('startDate','<',Carbon::now())
            ->first();
        $body_seachLot = [
                'numberTicket' => (string)$ticket->number,
            ];

            $body = $this->body();

            $user = User::where('email', $body['email'])->first();

            if ($user) {
                $user->email_verified_at  = Carbon::now();
                $user->save();
                $response = $this->json('POST', '/api/login', $body, ['Accept' => 'application/json'])
                    ->assertStatus(200)
                    ->assertJsonStructure(['access_token', 'token_type', 'expires_in', "user"]);
            } else {
                $response = $this->json('POST', '/api/register', $body, ['Accept' => 'application/json'])
                    ->assertStatus(201)
                    ->assertJsonStructure(['message', 'user', 'token']);
            }

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer ' . $response["access_token"],
                'Accept' => 'application/json'
            ])->post('/api/search-lot', $body_seachLot);

            $responseo->assertStatus(200);

       }

    public function testApiAuthorizedToRequestWithTicketUsed() {

        $ticket = Ticket::where('idUser','!=', null)->where('isUsed', 1)->first();

        if($ticket->isUsed and !empty($ticket->idUser)) {
         $body_seachLot = [
             'numberTicket' => (string) $ticket->number,
         ];

         $body = $this->body();

         $user = User::where('email',$body['email'] )->first();

         if($user) {
             $response = $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
                 ->assertStatus(200)
                 ->assertJsonStructure(['access_token','token_type','expires_in',"user"]);
         }else {
             $response = $this->json('POST','/api/register',$body,['Accept' => 'application/json'])
                 ->assertStatus(201)
                 ->assertJsonStructure(['message','user','token']);
         }

         $responseo = $this->withHeaders([
             'Authorization' => 'Bearer '.$response["access_token"],
             'Accept' => 'application/json'
         ])->post('/api/search-lot', $body_seachLot);

         $responseo->assertStatus(403);
     }

    }

    public function testApiAuthorizedToRequestWithInvalidTicketUsed() {

        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)->first();

        $ticket->startDate = Carbon::now();
        $ticket->endDate = Carbon::now();
        $ticket->save();

        $body_seachLot = [
                'numberTicket' => (string) $ticket->number
            ];

            $body = $this->body();

            $user = User::where('email',$body['email'] )->first();

            if($user) {
                $response = $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
                    ->assertStatus(200)
                    ->assertJsonStructure(['access_token','token_type','expires_in',"user"]);
            }else {
                $response = $this->json('POST','/api/register',$body,['Accept' => 'application/json'])
                    ->assertStatus(201)
                    ->assertJsonStructure(['message','user','token']);
            }

            $responseo = $this->withHeaders([
                'Authorization' => 'Bearer '.$response["access_token"],
                'Accept' => 'application/json'
            ])->post('/api/search-lot', $body_seachLot);

            $responseo->assertStatus(402);


    }

}
