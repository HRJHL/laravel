<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            if ($request->has('name') && $request->has('email') && $request->has('phone') && $request->has('identity') && $request->has('password')) {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'identity' => $request->input('identity'),
                    'password' => Hash::make($request->input('password')),
                ]);
                return response()->json(['message' => '회원가입이 완료되었습니다.'], 200);
            } else {
                return response()->json(['error' => '데이터가 누락되었습니다.'], 422);
            }
        } catch (\Exception $e) {
            Log::error('Error registering user: ' . $e->getMessage());

            return response()->json(['error' => '서버 오류가 발생했습니다.'], 500);
        }
    }
}
