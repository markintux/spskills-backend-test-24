<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prova extends Model
{
    use HasFactory;

    protected $table = 'provas';

    protected $fillable = ['questoes', 'finalizada', 'dificuldade', 'quantidade_questoes', 'materias', 'usuario'];

    protected $casts = [
        'questoes' => 'array',
        'materias' => 'array',
        'finalizada' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario');
    }

}
