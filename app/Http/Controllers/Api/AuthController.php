<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        return response()->json([
            "message" => "harap login terlebih dahulu"
        ], 200);
    }


    /**
     * Login
     */
    public function login(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Login Failed",
                "errors" => $validator->errors()
            ], 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        //if auth success
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user(),
            'token' => $token
        ], 200);
    }

    /**
     * Register
     */
    public function register(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Register Failed",
                "errors" => $validator->errors()
            ], 422);
        }

        //create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //return response JSON user is created
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user,
            ], 201);
        }

        //return JSON process insert failed
        return response()->json([
            'success' => false,
        ], 409);
    }

    /**
     * Login With Google
     */
    public function loginWithGoogle(Request $request)
    {
        $access_token = $request->bearerToken();
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

            $userdata = $response->json();

            // cek jika ada error
            if (isset($userdata['error'])) {
                return response()->json([
                    'status' => false,
                    'message' => "harap masukan token di postman (get new access token)",
                    "error" => "invalid credential"
                ], 500);
            }

            $user = User::where('email', $userdata['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $userdata['name'],
                    'email' => $userdata['email'],
                    'password' => bcrypt('password'),
                ]);
            }
            $token = auth()->guard('api')->login($user);
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error exception'
            ], 500);
        }
    }
}
