<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('identity', 'password');

            if (Auth::attempt($credentials)) {

                $user = Auth::user();
                return response()->json([
                    'message' => '로그인 되었습니다.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_image' => $user->profile_image,
                    ],
                ], 200);
            }

            return response()->json(['error' => '인증 실패.'], 401);
        } catch (\Exception $e) {
            Log::error('Error during login: ' . $e->getMessage());

            return response()->json(['error' => '서버 오류가 발생했습니다.'], 500);
        }
    }
}
