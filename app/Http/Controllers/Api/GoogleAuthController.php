<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callback(){
        try{
            $user = Socialite::driver('google')->user();
            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        }catch(\Exception $e){
            dd($e);
        }
    }
}
