<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit; // Credit 모델을 사용하기 위해 추가

class CreditController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'amount' => 'required|numeric',
                'orderName' => 'required|string',
                'customerName' => 'required|string',
            ]);

            // Store validated data in database using Credit 모델
            $credit = Credit::create([
                'amount' => $validatedData['amount'],
                'order_name' => $validatedData['orderName'],
                'customer_name' => $validatedData['customerName'],
            ]);

            return response()->json(['message' => '결제 정보가 성공적으로 저장되었습니다.', 'data' => $credit], 201);
        } catch (\Exception $e) {
            // Handle error
            \Log::error('결제 정보 저장 중 오류 발생: ' . $e->getMessage());

            return response()->json(['error' => '서버 오류가 발생했습니다.'], 500);
        }
    }
}
