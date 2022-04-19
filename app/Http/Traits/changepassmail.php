<?php
namespace App\Http\Traits;

use App\Mail\resetpassword;
use App\Models\auth\ResetPass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait changepassmail {
    public function sendlink($link, $email) {

        $hash_id = Str::random(60);
        $linkhash = $link . "reset/password/" . $hash_id;
        $data_upload = [
            "hash_id" => $hash_id,
            "email" => $email,
            "status" => 0,
        ];
        $details = [
            "title" => "Reset Password",
            "body" => 'Visit this link to change password',
            "button" => $linkhash,
        ];

        $addtodata = ResetPass::insert($data_upload);
        $sendmail = Mail::to($email)->send(new resetpassword($details));

        if($addtodata && $sendmail)
        return true;
        else
        return false;
    }
}
