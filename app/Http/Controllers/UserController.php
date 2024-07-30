<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function userinfo(Request $request)
    {
        \Log::info('Request Data: ', $request->all());
    
        try {
            $validatedData = $request->validate([
                'email' => 'required|string|email',
            ]);
    
            $users = User::where('email', $validatedData['email'])->get();
    
            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No payment information found for this user.'
                ], 404);
            }
    
            return response()->json([
                'data' => $users
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid input data.'], 422);
        } catch (\Exception $e) {
            \Log::error('Error retrieving payment information: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    public function changeId(Request $request)
    {
        Log::info('Request Data: ', $request->all());

        try {
            $validatedData = $request->validate([
                'identity' => 'required|string',
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $validatedData['email'])
                        ->first();

            if (is_null($user)) {
                return response()->json([
                    'message' => 'No User information found for this user.'
                ], 404);
            }

            $user->identity = $validatedData['identity'];
            $user->save();

            return response()->json([
                'message' => 'Password code sent successfully.',
                'data' => $user,
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error retrieving user information: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    public function changePw(Request $request)
    {
        Log::info('Request Data: ', [
            'email' => $request->input('email'),
            'password' => '***',
            'npassword' => '***',
        ]);

        try {
            $validatedData = $request->validate([
                'password' => 'required|string',
                'npassword' => 'required|string',
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $validatedData['email'])
                        ->first();

            if (is_null($user)) {
                return response()->json([
                    'message' => 'No User information found for this user.'
                ], 404);
            }

            if (!Hash::check($validatedData['password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.'
                ], 400);
            }

            $user->password = Hash::make($validatedData['npassword']);
            $user->save();

            return response()->json([
                'message' => 'Password code sent successfully.',
                'data' => $user,
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error retrieving user information: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    public function remove(Request $request)
{
    \Log::info('Request Data: ', $request->all());

    try {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (is_null($user)) {
            return response()->json([
                'message' => 'No user information found for this email.'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User has been successfully removed.'
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid input data.'], 422);
    } catch (\Exception $e) {
        \Log::error('Error removing user: ' . $e->getMessage());
        return response()->json(['error' => 'Server error occurred.'], 500);
    }
}

}