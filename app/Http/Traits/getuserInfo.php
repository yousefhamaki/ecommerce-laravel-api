<?php
namespace App\Http\Traits;

use App\Models\auth\AccessTokens;
use App\Models\auth\User;

trait getuserInfo {
    public function userInfo($token) {
        $userId = AccessTokens::where("token", "=", $token)->first();
        if(isset($userId->tokenable_id)){
            $userData = User::where("id", "=",$userId->tokenable_id)->first();
            return [
                "status" => true,
                "userdata" => $userData,
                "userid" => $userId,
                "rank" => $userData->rank,
            ];
        }
        return false;

    }
}
