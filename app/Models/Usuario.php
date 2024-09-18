<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $fillable = ['nome', 'username', 'email', 'senha', 'token'];
    protected $hidden = ['senha'];
 
    public function setSenhaAttribute($value)
    {
        $this->attributes['senha'] = bcrypt($value);
    }

}
