<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'language', 'is_active',
    ];

    protected $hidden = [
        'password', 'api_token',
    ];
    public function domains()
{
    return $this->hasMany(ClientDomain::class);
}
}
