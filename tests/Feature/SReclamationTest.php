<?php
namespace Tests\Feature;

use App\Models\HistoriqueGains;
use App\Models\Reclamation;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class SReclamationTest extends TestCase
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

    public function testStoreReclamationWithInvalidHistory() {

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
            'lieu_livraison' =>'15 rue de chatelet',
            'user_id' => '1',
            'phone'=> '0767890909',
            'history_id'=> '99999999'
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
        ])->post('/api/reclamation', $body_s);

        $responseo->assertStatus(400);
    }


    public function testStoreReclamationWithInvalidInput() {

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

        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)
            ->where('endDate','>=',Carbon::now())
            ->where('startDate','<',Carbon::now())
            ->first();
        $body_seachLot = [
            'numberTicket' => (string)$ticket->number,
        ];

        $responseHistory = $this->withHeaders([
            'Authorization' => 'Bearer ' . $response["access_token"],
            'Accept' => 'application/json'
        ])->post('/api/search-lot', $body_seachLot);

        $responseHistory->assertStatus(200);


        $histories =  HistoriqueGains::first();
        $histories->takenAt = null;
        $histories->save();

        $body_s = [
            'lieu_livraison' =>'15 rue de chatelet',
            'user_id' => $user->id,
            'history_id'=> $histories->id
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
        ])->post('/api/reclamation/', $body_s);

        $responseo->assertStatus(401);
    }


    public function testStoreReclamationWithValidInput() {

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
        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)
            ->where('endDate','>=',Carbon::now())
            ->where('startDate','<',Carbon::now())
            ->first();
        $body_seachLot = [
            'numberTicket' => (string)$ticket->number,
        ];

        $responseHistory = $this->withHeaders([
            'Authorization' => 'Bearer ' . $response["access_token"],
            'Accept' => 'application/json'
        ])->post('/api/search-lot', $body_seachLot);

        $responseHistory->assertStatus(200);

        $histories =  HistoriqueGains::first();
        $histories->takenAt = null;
        $histories->save();

        $body_s = [
            'lieu_livraison' =>'15 rue de chatelet',
            'user_id' => $user->id,
            'history_id'=> $histories->id,
            'phone'=> '0767890909'
        ];


        if(isset($response["access_token"]) and !empty($response["access_token"])) {
            $token =$response["access_token"];
        }

        if(isset($response["token"]) and !empty($response["token"])) {
            $token =$response["token"];
        }

       $reclamation = Reclamation::where('history_id',$histories->id)->first();
        if(isset($reclamation) and !empty($reclamation)){
            $search = true;
        }else {
            $search = false;
        }
        $responseo = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post('/api/reclamation',$body_s);

        if($search) {
            $responseo->assertStatus(400);
        }else {
            $responseo->assertStatus(200);
        }

    }


    public function testStoreReclamationWithInValidUser() {

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
        $histories =  HistoriqueGains::first();
        $histories->takenAt = null;
        $histories->save();

        $body_s = [
            'lieu_livraison' =>'15 rue de chatelet',
            'user_id' => '190900',
            'phone'=> '0767890909',
            'history_id'=> $histories->id
        ];

        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)
            ->where('endDate','>=',Carbon::now())
            ->where('startDate','<',Carbon::now())
            ->first();
        $body_seachLot = [
            'numberTicket' => (string)$ticket->number,
        ];

        $responseHistory = $this->withHeaders([
            'Authorization' => 'Bearer ' . $response["access_token"],
            'Accept' => 'application/json'
        ])->post('/api/search-lot', $body_seachLot);

        $responseHistory->assertStatus(200);



        if(isset($response["access_token"]) and !empty($response["access_token"])) {
            $token =$response["access_token"];
        }

        if(isset($response["token"]) and !empty($response["token"])) {
            $token =$response["token"];
        }
        $responseo = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post('/api/reclamation/', $body_s);

        $responseo->assertStatus(400);
    }


    public function testStoreReclamationWithInValidTakenAt() {

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

        $histories =  HistoriqueGains::first();
        $histories->takenAt = new \DateTime();
        $histories->save();
        $body_s = [
            'lieu_livraison' =>'15 rue de chatelet',
            'user_id' => $user->id,
            'phone'=> '0767890909',
            'history_id' => $histories->id,
        ];

        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)
            ->where('endDate','>=',Carbon::now())
            ->where('startDate','<',Carbon::now())
            ->first();
        $body_seachLot = [
            'numberTicket' => (string)$ticket->number,
        ];

        $responseHistory = $this->withHeaders([
            'Authorization' => 'Bearer ' . $response["access_token"],
            'Accept' => 'application/json'
        ])->post('/api/search-lot', $body_seachLot);

        $responseHistory->assertStatus(200);

        if(isset($response["access_token"]) and !empty($response["access_token"])) {
            $token =$response["access_token"];
        }

        if(isset($response["token"]) and !empty($response["token"])) {
            $token =$response["token"];
        }
        $responseo = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post('/api/reclamation/', $body_s);

        $responseo->assertStatus(400);
    }




    public function testStoreReclamationWithInvalidHistoryId() {

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
        $ticket = Ticket::where('idUser', null)->where('isUsed', 0)
            ->where('endDate','>=',Carbon::now())
            ->where('startDate','<',Carbon::now())
            ->first();
        $body_seachLot = [
            'numberTicket' => (string)$ticket->number,
        ];

        $responseHistory = $this->withHeaders([
            'Authorization' => 'Bearer ' . $response["access_token"],
            'Accept' => 'application/json'
        ])->post('/api/search-lot', $body_seachLot);

        $responseHistory->assertStatus(200);

        $histories =  HistoriqueGains::first();
        $histories->takenAt = null;
        $histories->save();

        $body_s = [
            'lieu_livraison' =>'15 rue de chatelet',
            'user_id' => $user->id,
            'history_id'=> 9090909,
            'phone'=> '0767890909'
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
        ])->post('/api/reclamation',$body_s);

        $responseo->assertStatus(400);
    }


}
