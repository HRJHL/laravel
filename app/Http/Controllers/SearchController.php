<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordCode;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function searchL(Request $request)
    {
        Log::info('Request Data: ', $request->all());

        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $validatedData['email'])->where('name', $validatedData['name'])->first();

            if (is_null($user)) {
                return response()->json([
                    'message' => 'No User information found for this user.'
                ], 404);
            }

            return response()->json([
                'data' => $user
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error retrieving user information: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    public function searchP(Request $request)
    {
        Log::info('Request Data: ', $request->all());

        try {
            $validatedData = $request->validate([
                'identity' => 'required|string',
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $validatedData['email'])
                        ->where('identity', $validatedData['identity'])
                        ->first();

            if (is_null($user)) {
                return response()->json([
                    'message' => 'No User information found for this user.'
                ], 404);
            }

            $verificationCode = Str::random(8);
            $user->password = Hash::make($verificationCode);
            $user->save();

            Mail::to($validatedData['email'])->send(new \App\Mail\PasswordCode($verificationCode));

            return response()->json([
                'message' => 'Password code sent successfully.',
                'data' => $user,
                'code' => $verificationCode,
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error retrieving user information: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }
}