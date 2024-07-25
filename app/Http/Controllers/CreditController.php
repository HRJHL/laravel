<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit;

class CreditController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'amount' => 'required|numeric',
                'orderName' => 'required|string',
                'customerName' => 'required|string',
            ]);

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

    public function datab(Request $request)
{
    \Log::info('Request Data: ', $request->all());

    try {
        $validatedData = $request->validate([
            'customerName' => 'required|string',
        ]);

        $credits = Credit::where('customer_name', $validatedData['customerName'])->get();

        if ($credits->isEmpty()) {
            return response()->json([
                'message' => 'No payment information found for this user.'
            ], 404);
        }

        return response()->json([
            'data' => $credits
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid input data.'], 422);
    } catch (\Exception $e) {
        \Log::error('Error retrieving payment information: ' . $e->getMessage());
        return response()->json(['error' => 'Server error occurred.'], 500);
    }
}


}
