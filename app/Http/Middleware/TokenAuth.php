<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\auth\AccessTokens;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!isset($request->usertoken)){
            return response()->json([
                "status" => false,
                "errortype" => "tokenParam",
                "errormessage" => "To make this request must be login"
            ]);
        }
        $usertoken = AccessTokens::where("token", "=", $request->usertoken)
                        ->where("status", "=", 1)
                        ->first();
        if(!isset($usertoken->tokenable_id)){
            return response()->json([
                "status" => false,
                "errortype" => "tokenvalid",
                "errormessage" => "Failed Please login again"
            ]);
        }
        return $next($request);
    }
}
