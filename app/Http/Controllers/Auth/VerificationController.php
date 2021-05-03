<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use URL;

class VerificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }


    public function verify(Request $request, User $user){

        if (! $request->hasValidSignature($request)){

            return response()->json(["errors" => [
                "message" => "Invalid verification link"
            ]],422);

        }


        if ($user->hasVerifiedEmail()){

            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]],422);

        }


        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email Successfully Verified'],200);

    }

    public function resend(Request $request){

        $this->validate($request,[
           'email' => ['email','required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user){
            return response()->json(["errors" => [
                "email" => "No user found"]],422);
        }


        if ($user->hasVerifiedEmail()){

            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]],422);

        }

        $user->sendEmailVerificationNotification();

        return response()->json(["status"=>"Verification link resent"]);


    }
}
