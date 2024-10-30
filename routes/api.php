<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/{auth_provider}/login', function ($auth_provider, Request $request) {
    $request->validate([
       'auth_token' => 'required',
    ]);
    $provider_user = Socialite::driver($auth_provider)->userFromToken($request->auth_token);

    $user = User::where('email', $provider_user->email)->first();
    if ($user == null) {
        $user = User::create([
            'name' => $provider_user->name,
            'email' => $provider_user->email,
            'provider_name' => $auth_provider,
            'provider_id' => $provider_user->id,
        ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});
