<?php
namespace App\Http\Traits;
use Illuminate\Support\Facades\Validator;

trait ValidatationTrait {
    public function check_validate($req, $validationType){
        $validator = $validator = Validator::make($req->all(), $validationType);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                "error_type" => "validate",
                'details' => $validator->errors()
            ]);
        }else{
            return "true";
        }
    }
}
