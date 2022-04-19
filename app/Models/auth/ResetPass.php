<?php

namespace App\Models\auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPass extends Model
{
    use HasFactory;
    protected $table = "reset_passwords";
    protected $guarded = [];
    public $timestamps = true;
}
