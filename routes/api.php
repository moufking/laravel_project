<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LotController;
use App\Http\Controllers\API\ReclamationController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
        Route::middleware(['cors'])->group(function () {

            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/register_with_socialite', [AuthController::class, 'registerWithSocialite']);
            Route::post('/login_with_socialite', [AuthController::class, 'loginWithSocialite']);
            Route::post('/forget_password', [AuthController::class, 'forget_password']);

            Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

            // Resend link to verify email
            Route::post('/email/verify/resend', [VerifyEmailController::class, 'resend'])->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

            Route::get('/email/verify', [VerifyEmailController::class, 'show'])->name('verification.notice');


            Route::group(['middleware' => ['jwt.verify','verified']], function () {
                Route::post('/logout', [AuthController::class, 'logout']);
                Route::post('/refresh', [AuthController::class, 'refresh']);
                Route::get('/user-profile', [AuthController::class, 'userProfile'])->name('user-profile');
                Route::post('/user/update/{id}', [AuthController::class, 'updateInformation'])->name('update-information');
                Route::get('/user/all_reclamation', [AuthController::class, 'getAllReclamation'])->name('all-reclamation');
                Route::get('/user/all_historical', [AuthController::class, 'getAllHistorical'])->name('all-historical');
                Route::get('/user/delete-account/{idUser}', [AuthController::class, 'deleteAccount'])->where('idUser',  '[0-9]+')->name('deleteAccount');
                /*
                 * ticket
                 */
                Route::post('/search-lot', [LotController::class, 'searchLot']);
                Route::post('/getATickerNumber', [TicketController::class, 'getATicketNumber']);

                /*
                 * Reclamation
                 */

                Route::post('/reclamation/', [ReclamationController::class, 'store']);


                Route::post('/update_password', [AuthController::class, 'update_password']);
            });

        });


