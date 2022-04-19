<?php

namespace App\Models\auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessTokens extends Model
{
    use HasFactory;

    protected $table = "personal_access_tokens";
    protected $fillable = [
        'id', 'tokenable_type', 'tokenable_id', 'name', 'ip', 'token',
        'abilities', 'last_used_at', 'status', 'created_at', 'updated_at'
    ];
}
