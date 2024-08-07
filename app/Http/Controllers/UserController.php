<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function userinfo(Request $request)
    {
        \Log::info('Request Data: ', $request->all());
    
        try {
            $validatedData = $request->validate([
                'email' => 'required|string',
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
                'email' => 'required|string',
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
                'email' => 'required|string',
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

    public function changeAll(Request $request)
    {
        Log::info('Request Data: ', [
            'email' => $request->input('email'),
            'identity' => 'required|string',
            'password' => '***',
            'npassword' => '***',
        ]);

        try {
            $validatedData = $request->validate([
                'identity' => 'required|string',
                'password' => 'required|string',
                'npassword' => 'required|string',
                'email' => 'required|string',
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

    public function changeImage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'required|email|exists:users,email',
        ]);

        // Handle the file upload
        $path = null;
        if ($request->file('image')) {
            $file = $request->file('image');
            $path = $file->store('images', 'public'); // Store file in 'images' folder
        }

        // Find the user by email and update their profile with the new image path
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->profile_image = $path; // Assume you have a 'profile_image' column in your 'users' table
            $user->save();
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'message' => 'Image uploaded and user updated successfully'
        ]);
    }


    public function remove(Request $request)
{
    \Log::info('Request Data: ', $request->all());

    try {
        $validatedData = $request->validate([
            'email' => 'required|string',
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