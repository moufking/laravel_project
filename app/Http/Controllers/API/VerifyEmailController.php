<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;


class VerifyEmailController extends Controller
{

    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            //. '/email/verify/already-success'
            return Redirect::to(env('FRONT_URL'));

        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        //. '/email/verify/success'
        return Redirect::to(env('FRONT_URL'));
    }

    public function resend(Request $request)
    {
        if ($request->user()) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json([
                'message' => $request->user(),
            ], 200);
        } else {
            return response()->json([
                'message' => "L'utilisateur n'existe pas dans la base de données",
            ], 200);
        }
    }

    public function show()
    {
        return response()->json(['message' => 'Veuillez confirmer votre courrier'], 200);
    }

}
