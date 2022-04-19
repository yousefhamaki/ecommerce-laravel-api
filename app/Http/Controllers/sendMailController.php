<?php

namespace App\Http\Controllers;

use App\Mail\sendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class sendMailController extends Controller
{
    public function sendactivationCode(Request $req)
    {
        $randomNumber = random_int(100000, 999999);

        $details = [
            "title" => "E-mail Activation",
            "body" => 'this is your activation code ' . $randomNumber,
        ];

        Mail::to($req->to_email)->send(new sendMail($details));
        return response()->json([
            "status" => true,
            "mail" => true,
            "message" => "we have send a verification code to you. \n please check your E-mail"
        ]);
    }
}
