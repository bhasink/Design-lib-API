<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

   public function attemptLogin(Request $request){

       $token = $this->guard()->attempt($this->credentials($request));

       if (! $token){
           return false;
       }

       //get auth user
       $user = $this->guard()->user();

       if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
           return false;
       }

       //set the user's token
       $this->guard()->setToken($token);

       return true;

   }

   protected function sendLoginResponse(Request $request){

       $this->clearLoginAttempts($request);

       //get token from jwt
       $token = (string)$this->guard()->getToken();

       //exp of token

       $expiration = $this->guard()->getPayload()->get('exp');

       return response()->json([
          'token' => $token,
          'token_type' => 'bearer',
          'expires_in' => $expiration
       ]);

   }

   protected function sendFailedLoginResponse(){

       $user = $this->guard()->user();

       if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
           return response()->json(["errors" => [
               "verification" => "you need to verify"
           ]]);
       }

       throw ValidationException::withMessages([
           $this->username() => "Authentication failed"
       ]);
   }

   public function logout(){
       $this->guard()->logout();
       return response()->json(['message' => 'Logged out']);
   }

}
