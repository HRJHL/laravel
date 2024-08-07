<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoogleController extends Controller
{
    public function authenticate(Request $request)
    {
        $accessToken = $request->input('access_token');

        $client = new Client();
        $response = $client->get('https://www.googleapis.com/oauth2/v2/userinfo', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}"
            ]
        ]);

        $userInfo = json_decode($response->getBody()->getContents(), true);

        if (isset($userInfo['id'])) {
            $googleId = $userInfo['id'];
            $email = $userInfo['email'];
            $name = $userInfo['name'];

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'name' => $name,
                    'identity' => $email,
                    'password' => Hash::make(Str::random(24)), 
                    'profile_image' => $userInfo['picture'] ?? null,
                ]);
            }

            Auth::login($user);

            return response()->json([
                'success' => true,
                'user' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'profile_image' => $user->profile_image,
                ]
            ]);
        } else {
            return response()->json(['success' => false], 401);
        }
    }

    public function handleGoogleLogin(Request $request)
    {
        $idToken = $request->input('id_token');

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $client->setAuthConfig([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        ]);

        try {
            $payload = $client->verifyIdToken($idToken);

            if (!$payload) {
                return response()->json(['success' => false, 'error' => 'Invalid ID token'], 401);
            }

            $email = $payload['email'];
            $name = $payload['name'];

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'name' => $name,
                    'password' => Hash::make(Str::random(24)), // Generate a random password
                    'profile_image' => $payload['picture'] ?? null, // Store profile image if available
                ]);
            }

            Auth::login($user);

            return response()->json([
                'success' => true,
                'user' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'profile_image' => $user->profile_image,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        $code = $request->input('code');

        $client = new Client();
        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
                'code' => $code,
            ]
        ]);

        $tokenInfo = json_decode($response->getBody()->getContents(), true);
        $accessToken = $tokenInfo['access_token'];

        return $this->authenticate(new Request(['access_token' => $accessToken]));
    }
}
