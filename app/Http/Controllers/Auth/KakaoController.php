<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use GuzzleHttp\Client;

class KakaoAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $accessToken = $request->input('access_token');

        // 카카오 API를 통해 사용자 정보 요청
        $client = new Client();
        $response = $client->get('https://kapi.kakao.com/v2/user/me', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}"
            ]
        ]);

        $userInfo = json_decode($response->getBody()->getContents(), true);

        // 사용자 정보 저장 및 로그인 처리
        $user = User::where('kakao_id', $userInfo['id'])->first();
        if (!$user) {
            $user = User::create([
                'name' => $userInfo['properties']['nickname'],
                'email' => $userInfo['kakao_account']['email'],
                'kakao_id' => $userInfo['id'],
            ]);
        }

        Auth::login($user);

        return response()->json(['user' => $user]);
    }

    public function handleCallback(Request $request)
    {
        // 콜백에서 코드를 받아 액세스 토큰을 요청
        $code = $request->input('code');

        $client = new Client();
        $response = $client->post('https://kauth.kakao.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('KAKAO_CLIENT_ID'),
                'redirect_uri' => env('KAKAO_REDIRECT'),
                'code' => $code,
            ]
        ]);

        $tokenInfo = json_decode($response->getBody()->getContents(), true);
        $accessToken = $tokenInfo['access_token'];

        // 액세스 토큰을 사용하여 사용자 정보를 요청하고 로그인 처리
        return $this->authenticate(new Request(['access_token' => $accessToken]));
    }
}

