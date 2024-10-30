<?php

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/**
 * @param $provider_user
 * @return JsonResponse
 */
function getUser($provider_user): JsonResponse
{
    $user = User::where('email', $provider_user->email)->first();
    if ($user == null) {
        $user = User::create([
            'name' => $provider_user->name,
            'email' => $provider_user->email,
        ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
}

Route::post('/facebook/login', function (Request $request) {
    $request->validate([
       'auth_token' => 'required',
    ]);
    $provider_user = Socialite::driver('facebook')->userFromToken($request->auth_token);

    return getUser($provider_user);
});


Route::post('/gg-android/register', function (Request $request) {
    $request->validate([
        'auth_token' => 'required',
    ]);
    $provider_user = Socialite::buildProvider('google', [
        'client_id' => '',
        'client_secret' => config('services.google.client_id'),
    ])->userFromToken($request->auth_token);

    return getUser($provider_user);
});

Route::post('/gg-ios/register', function (Request $request) {
    $request->validate([
        'auth_token' => 'required',
    ]);
    $provider_user = Socialite::buildProvider('google', [
        'client_id' => '',
        'client_secret' => config('services.google.client_secret'),
    ])->userFromToken($request->auth_token);

    return getUser($provider_user);
});
