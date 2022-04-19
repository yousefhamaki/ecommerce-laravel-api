<?php
namespace App\Http\Traits;

use App\Mail\resetpassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait changepassmail {
    public function sendlink($link, $email) {

        $linkhash = $link . "reset/password/" . Str::random(60);

        $details = [
            "title" => "Reset Password",
            "body" => 'Visit this link to change password',
            "button" => $linkhash,
        ];

        Mail::to($email)->send(new resetpassword($details));

        return true;
    }
}
