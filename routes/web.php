<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\sendMailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::post("/login", [userController::class, "login"]);
//'f_name','l_name,'phone',"zip_code", 'app_token','email','password',"app_type"
    Route::post("/signup", [userController::class, "create"]);
    Route::post("/check/username", [userController::class, "check_username"]);
    Route::post("/reset/password", [userController::class, "sendresetlink"]);
    Route::post("/reset/user/check", [userController::class, "search_email_toreset"]);
    Route::post("/reset/password/activate", [userController::class, "changepassbyverified"]);

    //change user info
    Route::prefix('change')->middleware("authapi")->group(function () {
        Route::post("username", [userController::class, "changeusername"]);
        Route::post("password", [userController::class, "changepassword"]);
    });
    Route::post("sendactivationcode", [sendMailController::class, "sendactivationCode"]);

    Route::prefix('categories')->middleware("authapi")->group(function () {
        Route::post("add", [CategoriesController::class, "addcategory"]);
    });
});
