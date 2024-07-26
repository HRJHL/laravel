<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailSendController extends Controller
{
    public function mailSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $email = $request->input('email');
        $verificationCode = Str::random(4);

        Mail::to($email)->send(new \App\Mail\VerificationCode($verificationCode));

        return response()->json([
            'message' => 'Verification code sent.',
            'verification_code' => $verificationCode
        ]);
    }
}
