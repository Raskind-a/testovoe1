<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $name = Str::before($request->email, '@');
            $token = $user->createToken($name);
            //->plainTextToken
//            $request->session()->regenerate();

            return response()->json([
                'result' => [
                    'bearer token' => $token
                ]
            ]);
        }

        return response()->json([
            'error' => 'wrong email or password'
        ]);
    }
}
