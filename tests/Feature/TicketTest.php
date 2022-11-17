<?php
namespace Tests\Feature;

use App\Models\HistoriqueGains;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class TicketTest extends TestCase
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
                'canLegalyPlay' => 1,
                'newsletter' => 1,
                'ville' => 'Antony',
                'email'=> 'admin9000@gmail.com',
                'email_verified_at'=> Carbon::now()
            ];
    }

    public function testGetATicketNumberWithInvalidPrice() {

        $body = $this->body();

        $user = User::where('email',$body['email'] )->first();

        if($user) {
            $user->email_verified_at  = Carbon::now();
            $user->save();
            $response = $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token','token_type','expires_in',"user"]);
        }else {
            $response = $this->json('POST','/api/register',$body,['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message','user','token']);
            //$user->email_verified_at  = Carbon::now();
            //$user->save();
        }
        $body_s = [
            'prixCommande' => 25
        ];

        if(isset($response["access_token"]) and !empty($response["access_token"])) {
            $token =$response["access_token"];
        }

        if(isset($response["token"]) and !empty($response["token"])) {
            $token =$response["token"];
        }
        $responseo = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post('/api/getATickerNumber', $body_s);

        $responseo->assertStatus(404);
    }


    public function testGetATicketNumberWithValidPrice() {

        $body = $this->body();

        $user = User::where('email',$body['email'] )->first();

        if($user) {
            $user->email_verified_at  = Carbon::now();
            $user->save();
            $response = $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure(['access_token','token_type','expires_in',"user"]);
        }else {
            $response = $this->json('POST','/api/register',$body,['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure(['message','user','token']);
            $user->email_verified_at  = Carbon::now();
            $user->save();
        }
        $body_s = [
            'prixCommande' => 75
        ];

        if(isset($response["access_token"]) and !empty($response["access_token"])) {
            $token =$response["access_token"];
        }

        if(isset($response["token"]) and !empty($response["token"])) {
            $token =$response["token"];
        }
        $responseo = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post('/api/getATickerNumber', $body_s);

        $responseo->assertStatus(200);
    }

}
