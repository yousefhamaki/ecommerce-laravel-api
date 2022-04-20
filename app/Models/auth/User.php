<?php

namespace App\Models\auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "users";
    protected $fillable = [
        'id', 'hash_id', 'f_name', 'l_name', 'phone', 'zipcode', 'img','status', 'username ','google_id',
        'email', 'api_token', 'rank', 'created_at', 'updated_at'
    ];
    protected $hidden = ["password"];
}
