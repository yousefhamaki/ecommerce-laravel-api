<?php
namespace App\Http\Traits;

use App\Models\auth\AccessTokens;
use App\Models\auth\User;
use App\Http\Traits\getuserInfo;

trait getRank {
    use getuserInfo;
    public function userRank($token, $rank) {
        $Info = $this->userInfo($token);
        if($Info["rank"] < $rank){
            return response()->json([
                "status" => false,
                "errortype" => "rankerror",
                "message" => "unable to use this link"
            ]);
        }
        return "true";
    }
}
