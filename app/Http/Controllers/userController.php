<?php

namespace App\Http\Controllers;

use App\Http\Requests\user\loginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\auth\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Traits\createJson;
use App\Http\Traits\changepassmail;
use App\Http\Traits\ValidatationTrait;
use App\Http\Traits\getuserInfo;

class userController extends Controller
{
    use createJson, ValidatationTrait, getuserInfo, changepassmail;

    public function search_username($user)
    {
        $status = User::where("user_name", "=", $user)->get();
        if(count($status) > 0){
            return false;
        }
        return true;
    }

    public function update(Request $request)
    {
        $token = Str::random(60);
        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return ['token' => $token];
    }

    public function create(Request $req)
    {
        //validation
        $validate = $this->check_validate($req, $this->signupRequired);
        if($validate !== "true"){return $validate;}
        //make username unique
        $username = $req->f_name . $req->l_name . rand(pow(10, 8 - 1), pow(10, 8) -1);
        for($i = 0; $i > 100; $i++){
            if($this->checkusernameValid($username) == response()->json($this->validUsername)){
                break;
            }
            $username = $req->f_name . rand(pow(10, 8 - 1), pow(10, 8) -1) . $req->l_name;
        }
        //make values to add user
        $token = Str::random(60);
        $hash = Str::random(60);
        $setting = $this->makeJson($this->settingDefault);
        $images = $this->makeJson($this->images);
        $password = Hash::make($req->password);
        $userinfo = [
            'hash_id' => $hash,
            'f_name' => $req->f_name,
            'l_name' => $req->l_name,
            'email' =>$req->email,
            // 'ip' =>$user_ip,
            'l_name' => $req->l_name,
            'username' => $username,
            'phone' =>$req->phone,
            "zipcode" => $req->zip_code,
            "password" => $password,
            'img' => null,
            'api_token' => $token,
            "status" => 0,
            "setting" => $setting,
            "img" => $images,
        ];

        try{
            $add = User::insert($userinfo);
            if($add){
                $user_id = User::where("email", "=", $req->email)->first();
                $token = $user_id->createToken('API Token')->plainTextToken;
                [$id, $user_token] = explode('|', $token, 2);
                $signupdata = [
                    'success' => true,
                    'details' =>[
                        'token' => $user_token,
                        'login' => true,
                        'f_name' => $req->f_name,
                        'email' =>$req->email,
                        'l_name' => $req->l_name,
                        'user_name' => $username,
                        "setting" => json_decode($setting),
                        "img" => json_decode($images),
                    ]
                    ];
                return response()->json($signupdata);
            }
            return response()->json($this->errorContinue);

        }catch (ModelNotFoundException $exception) {
            return response()->json([
                "status"=> false,
                "message" => $exception->getMessage(),
                "error_type" => "failedSign",
            ]);
        }
    }

    public function login(Request $req)
    {
        //validation
        $validate = $this->check_validate($req, $this->loginRequired);
        if($validate !== "true"){return $validate;}
        //check user
        $user = User::where("email", '=', $req->email)->first();
        if(!isset($user->id) || !Hash::check($req->password, $user->password)){
            return response()->json($this->unauthReturn);
        }
        //make auth
        $token = $user->createToken($req->app_type, ['rank:user', 'server:check'])->plainTextToken;
        [$id, $user_token] = explode('|', $token, 2);

        $apiauth = [
            'status' => true,
            'auth' => true,
            'success' => true,
            'details' =>[
                'token' => $user_token,
                'login' => true,
                'f_name' => $user->f_name,
                'email' =>$user->email,
                'l_name' => $user->l_name,
                'user_name' => $user->username,
                "setting" => json_decode($user->setting),
                "img" => json_decode($user->img),
            ]
        ];
        //return api auth
        return response()->json($apiauth);
    }

    public function check_username(Request $req)
    {
        //validation
        $validate = $this->check_validate($req, $this->checkUsernameValidation);
        if($validate !== "true"){return $validate;}
        //result
        return $this->checkusernameValid($req->username);
    }

    //change
    public function changeusername(Request $req)
    {
        //validation
        $validate = $this->check_validate($req, $this->changeUsernameValidation);
        if($validate !== "true"){return $validate;}
        //get user id
        $userInfo = $this->userInfo($req->usertoken);
        if($this->checkusernameValid($req->username) == response()->json($this->validUsername)){
            User::where("id", "=", $userInfo["userdata"]->id)->update([
                "username" => $req->username,
            ]);
            return response()->json([
                "status" => true,
                "message" => "Username was updated successfully"
            ]);
        }
        return $this->checkusernameValid($req->username);
    }

    public function changepassword(Request $req)
    {
        //validation
        $validate = $this->check_validate($req, $this->changepasswordValidation);
        if($validate !== "true"){return $validate;}
        $passwordCheck = $this->passwordCheck($req->newpassword);

        if($req->oldpassword === $req->newpassword){
            return response()->json([
                "status" => false,
                "newpassword" => false,
                "message" => "you can't use the old password in new password"
            ]);
        }
        if($passwordCheck !== true){
            return $passwordCheck;
        }
        //get user id
        $userInfo = $this->userInfo($req->usertoken);
        if(Hash::check($req->oldpassword, $userInfo["userdata"]->password)){
            User::where("id", "=", $userInfo["userdata"]->id)->update([
                "password" => Hash::make($req->newpassword),
            ]);
            return response()->json([
                "status" => true,
                "message" => "your password was shanged successfully"
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "your old password is not correct"
        ]);
    }

    public function changepass(Request $req)
    {
        //validation check
        $validate = $this->check_validate($req, $this->resetpassValidation);
        if($validate !== "true"){return $validate;}
        //set function needs
        $link = $req->app_link;
        $email = $req->email;
        //send mail
        $mail = $this->sendlink($link, $email);
        if($mail){
            return "true";
        }
        return false;
    }


    //private
    private $resetpassValidation = [
        "email" => "required",
        "app_link" => "required",
    ];
    private $changeUsernameValidation = [
        "app_type" => "required",
        "app_token" => "required",
        "username" => "required",
        "usertoken" => "required",
    ];
    private $changepasswordValidation = [
        "app_type" => "required",
        "app_token" => "required",
        "oldpassword" => "required:60",
        "newpassword" => "required|max:60|min:8",
    ];
    private $validUsername = [
        "status" => true,
        "username" => true,
    ];
    private $checkUsernameValidation = [
        "app_type" => "required",
        "app_token" => "required",
        "username" => "required"
    ];
    private $settingDefault = [
        "dark" => true
    ];
    private $images = [
        "default" => "http://127.0.0.1:8000/data/user/profile/profile.jpg",
        "images" => null
    ];

    private $unauthReturn = [
        'status' => false,
        'status_code' => 500,
        'errortype' => 'Unautorized',
        "message" => "Email or password isn't correct"
    ];

    private $loginRequired = [
        'email' => 'required|max:255',
        'password' => 'required|max:255',
        "app_type" => "required",
        "app_token" => "required",
    ];
    private $signupRequired = [
        'f_name' => 'required|max:50',
        'l_name' => 'required|max:50',
        'phone' => 'required|unique:users|max:50',
        "zip_code" => "required|max:20",
        'app_token' => 'required',
        'email' => 'required|unique:users|max:255',
        'password' => 'required|max:255',
        "app_type" => "required",
        "app_token" => "required",
    ];

    private $errorContinue = [
        "status"=> false,
        "message" => "please try again",
        "error_type" => "failedcontinue",
    ];

    private function checkusernameValid($username)
    {
        $blacklistArray = [" ", ",", ".", "@", "#", "$", "%", "^", "&", "(", ")", "'",
                            '"', ";", ":", "/", "|", "\\", "*", "+", "?"];
        //valid length
        if(strlen($username) < 6){
            return response()->json([
                "status" => true,
                "username" => false,
                "message" => "username must be more than 5",
            ]);
        }
        //valid characters
        $characterError = "";
        foreach($blacklistArray as $d){
            if(str_contains($username, $d)){
                $characterError = $d;
            }
        }
        if($characterError !== ""){
            return response()->json([
                "status" => true,
                "username" => false,
                "message" => $characterError . " mustn't be in username",
            ]);
        }


        $usernamedata = User::where("username", "=", $username)->first();

        if(isset($usernamedata->id)){
            return response()->json([
                "status" => true,
                "username" => false,
                "message" => "username is already exist",
            ]);
        }
        return response()->json($this->validUsername);
    }

    private function passwordCheck($newpass)
    {
        $specialarray = [" ", ",", ".", "@", "#", "$", "%", "^", "&", "(", ")", "'",
                            '"', ";", ":", "/", "|", "\\", "*", "+", "?"];
        $special = 0;
        $errorReturn = [
            "status" => true,
            "password" => false,
        ];
        $lowerpass = strtolower($newpass);

        foreach($specialarray as $d){
            if(str_contains($newpass, $d)){
                $special = $special + 1;
            }
        }

        if($lowerpass === $newpass){
            $errorReturn["message"] = "your password must have capital charactes";
            return response()->json($errorReturn);
        }

        if($special < 1){
            $errorReturn["message"] = "your password must have special charactes";
            return response()->json($errorReturn);
        }

        return true;
    }
}
