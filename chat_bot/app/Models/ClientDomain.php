<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientDomain extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'domain'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
