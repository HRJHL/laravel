<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'message' => 'required|string',
        ]);

        Chat::create([
            'email' => $request->input('email'),
            'message' => $request->input('message'),
        ]);

        return response()->json(['success' => 'Message saved successfully.']);
    }

    public function chatM(Request $request)
    {
        \Log::info('Request Data: ', $request->all());

        try {
            $validatedData = $request->validate([
                'email' => 'required|string',
            ]);

            $chats = Chat::all();

            if ($chats->isEmpty()) {
                return response()->json([
                    'message' => 'No payment information found for this user.'
                ], 404);
            }

            return response()->json([
                'data' => $chats
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid input data.'], 422);
        } catch (\Exception $e) {
            \Log::error('Error retrieving payment information: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    public function talk(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string',
            'messageId' => 'required|integer',
            'talk' => 'required|string',
        ]);

        $chat = Chat::where('id', $validated['messageId'])
                    ->first();

        if ($chat) {
            $chat->talk = $validated['talk'];
            $chat->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Chat not found'], 404);
    }

    public function talkM(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|string',
            ]);
    
            $chats = Chat::where('email', $validatedData['email'])->get();
    
            if (!$chats) {
                return response()->json([
                    'message' => 'No payment information found for this user.'
                ], 404);
            }
    
            return response()->json([
                'data' => $chats
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
