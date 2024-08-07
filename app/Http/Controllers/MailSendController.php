<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User; // Ensure you import the User model
use App\Mail\VerificationCode;

class MailSendController extends Controller
{
    public function mailSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $email = $request->input('email');

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'message' => 'Email already exists.'
            ], 409); // HTTP status code 409 Conflict
        }

        $verificationCode = Str::random(4);

        Mail::to($email)->send(new \App\Mail\VerificationCode($verificationCode));

        return response()->json([
            'message' => 'Verification code sent.',
            'verification_code' => $verificationCode
        ]);
    }
}
