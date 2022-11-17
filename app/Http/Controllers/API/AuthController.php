<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Mail\ForgetPassword;
use App\Mail\LotEmail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|regex:/^[a-zA-Z0-9Ññ]+@\w+\.com/',
            'password' => "required|string|min:6|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = JWTAuth::attempt($validator->validated())) {
            return response()->json(['error' => 'Identifiants incorrects'], 401);
        }

        if (! auth()->user()->hasVerifiedEmail()) {
            return response()->json(['error' => "Vous n'avez pas vérifié votre email."], 401);
        }


        return $this->createNewToken($token);

    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Utilisateur deconnecté avec succès.']);
    }

    /**
     * Register a User.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        //^[-'a-zA-Z0-9À-ÿ\s]+$
        $validator = Validator::make($request->all(), [
            'name' => "required|string|between:2,100|regex:/^[-'a-zA-ZÀ-ÿ\s]+$/",
            'email' => 'required|email|max:255|unique:users|regex:/^[a-zA-Z0-9Ññ]+@\w+\.com/',
            'password' => "required|string|confirmed|min:6|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'telephone' => 'required|string|between:5,15|regex:/^[-0-9\+]+$/',
            'address' => "required|string|max:255|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'postal_code' => 'required|int|min:5|regex:/^[-0-9\+]+$/',
            'ville' => "required|string|max:200|regex:/^[-'a-zA-ZÀ-ÿ\s]+$/",
            'canLegalyPlay' => 'required|boolean',
            'additional_address' => "string|max:200|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'newsletter' => 'boolean',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $canLegalyPlay = $request->input('canLegalyPlay');

        if ( !isset($canLegalyPlay) || !$canLegalyPlay ){
            return response()->json([
                'error' => 'Vous devez avoir plus de 18 ans ou avoir l\'autorisation de vos parents pour vous inscrire'
            ]);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            ['password2' => Crypt::encryptString($request->password)],
        ));

        event(new Registered($user));

        if (!$token = JWTAuth::attempt($validator->validated())) {
            return response()->json(['error' => "Vous n'est pas autorisé."], 401);
        }

        return response()->json([
            'message' => 'Inscription effectué avec succès.',
            'user' => $user,
            'token' => $token

        ], 201);
    }

    public function updateInformation(Request $request, $id)
    {
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'name' => "string|between:2,100|regex:/^[-'a-zA-ZÀ-ÿ\s]+$/",
            'email' => 'email|max:255|unique:users|regex:/^[a-zA-Z0-9Ññ]+@\w+\.com/',
            'password' => "string|confirmed|min:6|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'telephone' => 'string|between:5,15|regex:/^[-0-9\+]+$/',
            'address' => "string|max:255|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'postal_code' => 'int|min:5|regex:/^[-0-9\+]+$/',
            'ville' => "string|max:200|regex:/^[-'a-zA-ZÀ-ÿ\s]+$/",
            'canLegalyPlay' => 'boolean',
            'additional_address' => "max:200|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'newsletter' => 'boolean',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($user) {
            $user->update($request->all());
            return response()->json([
                'message' =>  "Les informations de l'utilisateur sont modifier avec succès.",
                'user' => $user
            ], 200);

        } else {
            return response()->json(['message' => 'Aucun utilisateur trouvé .'], 404);
        }
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return
            response()->json(['token' => $this->createNewToken(auth()->refresh())], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userProfile()
    {
        return response()->json([
            'detail' => auth()->user()
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken($token)
    {
        // 'expires_in' => auth()->factory()->getTTL() * 60,
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function getAllReclamation()
    {

        $user = Auth::user();

        if ($user) {
            return response()->json([
                "reclamation" => $user->getReclamations,
                "total_reclamation" => count($user->getReclamations),
            ], 200);
        }

    }

    public function getAllHistorical()
    {

        $user = Auth::user();
        $AllHistorique = [];

        foreach ($user->getHistorisqueDeGain as $historique) {

            if (isset($historique->getReclamation) && !empty($historique->getReclamation)) {
                $reclamation = true;
            } else {
                $reclamation = false;
            }

            $json = array(
                'id' => $historique->id,
                'idTicket' => $historique->idTicket,
                'idUser' => $historique->idUser,
                'name' => $historique->getUser->name,
                'name_lot' => $historique->getTicket->getLot->libelle,
                'created_at' => $historique->created_at,
                'updated_at' => $historique->updated_at,
                'takenAt' => $historique->takenAt,
                'reclamation' => $reclamation
            );
            array_push($AllHistorique, $json);
        }

        if ($user) {
            return response()->json([
                "historical" => $AllHistorique,
                "total_historical" => count($user->getHistorisqueDeGain),
            ], 200);
        }

    }

    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => "required|min:6|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'new_password' => "required|string|min:6|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'new_password_confirmation' => "required|same:new_password|min:6|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();


        $new_password = Hash::make($request->get('new_password'));
        $old_password = Hash::make($request->get('old_password'));

       if(!password_verify($request->get('old_password'),$user->password )) {
           return response()->json([
               "message" => "Votre ancien mot de passe n'est pas conforme.",

            ], 400);


       }else {
           $user->password = $new_password;
           $response = $user->save();

           if($response) {
               return response()->json([
                   "message" => " Mot de passe modifié avec succès.",
                   "user" => $user,
               ], 200);
           }
       }

    }

    public function forget_password(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|regex:/^[a-zA-Z0-9Ññ]+@\w+\.com/'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

     $search_user= User::where('email', $request->get('email')) ->first();

        if($search_user) {
            $randomNumber = rand();
                $search_user->password = Hash::make($randomNumber);
                $search_user->save();

            try{
                //'administrateur@chezmoi.com'
                Mail::to($search_user->email)
                    ->send(new ForgetPassword($search_user, $randomNumber ));
            } catch(Exception $e){
                Log::info("Error from LotController when sending LotEmail to user:". Auth::id(). " : ". $e->getMessage());
            }

            return response()->json([
                'message' => 'Mot de passe modifier avec succès.',
                'new_password' => $randomNumber,
            ], 200);

        } else {
            return response()->json([
                "message" => "Email n'est pas trouvé",
            ], 404);
        }
    }


    // Le front envoie les infos
    // Le back  vérifie si l'utilisateur est exitant
    // Si existant le back le login
    // Si inexistant on envoie un JSON (Compte inexistant, 401)
    // Le front reçois l'erreur et à partir de l'erreur affiche une popup pour confirmer qui a l'age légale et l'autorisation pour s'enregistrer
    // En cliquant sur "J'accepte", le front lance la fonction register et envoie encore les infos
    // Le back reçoit les infos et il est register
    public function loginWithSocialite( Request $request ) {

        $validation = Validator::make($request->all(), [
            'id'=>'required|string',
            'name' => "required|string|between:2,100|regex:/^[-'a-zA-ZÀ-ÿ\s]+$/",
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9Ññ]+@\w+\.com/',
            'provider'=>'required|string',
        ]);

        if ( $validation->fails() ){
            return response()->json($validation->errors());
        }

        $provider = $request->input('provider');

        if (!$provider || ($provider != 'GOOGLE' && $provider != 'FACEBOOK')) {
            return response()->json([
                'error' => 'action impossible'
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if( empty($user) ){
            return response()->json(['error' => 'Compte inexistant'], 401);
        }

        $idSocialite = $request->input('id');

        if(
            ($provider =="FACEBOOK" && $user->facebook_id == $idSocialite)
            || ($provider =="GOOGLE" && $user->google_id == $idSocialite)
        ){
            $token = JWTAuth::attempt([
                'email' => $user->email,
                'password' => Crypt::decryptString($user->password2)
            ]);

            if (!$token) {
                return response()->json(['error' => 'Accès refusé'], 401);
            }

            return response()->json([
                'user' => $user,
                'token' => $token

            ], 201);
        } else {
            return response()->json([
                'error'=>'Identifiants incorrects'
            ]);
        }
    }

    public function registerWithSocialite(Request $request) {

        $validation = Validator::make($request->all(), [
            'id'=>'required|string',
            'name' => "required|string|between:2,100|regex:/^[-'a-zA-ZÀ-ÿ\s]+$/",
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9Ññ]+@\w+\.com/',
            'provider'=>'required|string',
            'canLegalyPlay'=>'required|boolean',
        ]);

        if ( $validation->fails() ){
            return response()->json($validation->errors(),402);
        }

        if (!$request->input('canLegalyPlay') ){
            return response()->json([
                'error' => 'Vous devez avoir plus de 18 ans ou avoir l\'autorisation de vos parents pour vous inscrire'
            ], 401);
        }

        $provider = $request->input('provider');

        if (!$provider || ($provider != 'GOOGLE' && $provider != 'FACEBOOK')) {
            return response()->json([
                'error' => 'action impossible'
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        //si l'utilisateur était déjà enregistré
        //avec le formulaire standard ou l'un des réseaux sociaux
        if ( !empty( $user ) ) {


            // s'il n'est pas encore enregistré avec l'un de ces réseaux sociaux
            if ($provider == "FACEBOOK" && !$user->facebook_id) {
                $user->facebook_id = $request->input('id');
            } else if ($provider == "GOOGLE" && !$user->google_id) {
                $user->google_id =   $request->input('id');
            } else { // si l'utilisateur est déjà enregistré avec les résaux sociaux
                $this->loginWithSocialite($request);
            }

            $isSaved = $user->update();
            if ($isSaved) {
                $token = JWTAuth::attempt([
                    'email' => $user->email,
                    'password' => Crypt::decryptString($user->password2)
                ]);

                if (!$token) {
                    return response()->json(['error' => 'Accès refusé'], 401);
                }

                return response()->json([
                    'user' => $user,
                    'token' => $token

                ], 201);

            } else {
                return response()->json(['error' => 'Erreur interne au serveur '], 500);
            }

        }

        //si l'utilisateur est inexistant
        $newUser = $request->all('email', 'name', 'canLegalyPlay');

        if ($provider == "FACEBOOK") {
            $newUser['facebook_id'] = $request->input('id');
        } else if ($provider == "GOOGLE") {
            $newUser['google_id'] = $request->input('id');
        }

        $password = Str::random(20);
        $userCreated = User::create( array_merge(
            $newUser,
            ['password' => bcrypt( $password )],
            ['password2' => Crypt::encryptString( $password )],
            ['email_verified_at' => Carbon::now()]
        ));

        if ($userCreated->id) {
            $token = JWTAuth::attempt([
                'email' => $userCreated->email,
                'password' => $password
            ]);

            if (!$token) {
                return response()->json(['error' => 'Accès refusé'], 401);
            }

            return response()->json([
                'user' => $userCreated,
                'token' => $token

            ], 201);

        } else {
            return response()->json(['error' => 'Erreur interne au serveur '], 500);
        }
    }

    public function deleteAccount( $idUser ){
        if(!$idUser) {
            return response()->json([
                'error' => 'action impossible'
            ], 400);
        }

        $userToDelete = User::find($idUser);

        if( (int) $idUser != Auth::id() ){
            return response()->json([
                'error' => 'Accès non autorisé'
            ], 403);
        }

        if( !empty($userToDelete) && $userToDelete->name != "neant"){
            $userToDelete->name = 'neant';
            $userToDelete->email = 'neant'.$userToDelete->id;
            $userToDelete->password = 'neant';
            $userToDelete->telephone = 'neant';
            $userToDelete->address = 'neant';
            $userToDelete->additional_address = 'neant';
            $userToDelete->postal_code = 'neant';
            $userToDelete->ville = 'neant';
            $userToDelete->facebook_id = 'neant';
            $userToDelete->google_id = 'neant';
            $userToDelete->password2 = 'neant';

            $isSaved = $userToDelete->save();

            if( $isSaved ){
                return response()->json([
                    'message' => 'Utilisateur supprimé'
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Erreur survenu lors de la suppression du compte . Veuillez reessayer'
                ], 500);
            }

        } else {
            return response()->json([
                'error' => 'Utilisateur inexistant'
            ], 400);
        }
    }
}
