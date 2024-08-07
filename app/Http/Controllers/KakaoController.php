<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KakaoController extends Controller
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

        if (isset($userInfo['id'])) {
            // 사용자 정보가 성공적으로 수신되었을 때
            $userId = $userInfo['id'];
            $nickname = $userInfo['properties']['nickname'] ?? null;

            // 사용자 정보를 데이터베이스에 저장
            $user = User::updateOrCreate(
                ['email' => $userId,
                'name' => $nickname, 
                'identity' => $userId,
                'password' => $userId]
            );

            return response()->json([
                'success' => true,
                'user' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'profile_image' => $user->profile_image,
                ]
            ]);
        } else {
            // 사용자 정보 수신 실패
            return response()->json(['success' => false], 401);
        }
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

        // 액세스 토큰을 사용하여 사용자 정보를 요청하고 성공 여부만 반환
        return $this->authenticate(new Request(['access_token' => $accessToken]));
    }

    
}
