<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questao extends Model
{
    use HasFactory;

    protected $table = 'questoes';

    protected $fillable = ['enunciado','materia','dificuldade'];

    public function opcoes()
    {
        return $this->hasMany(QuestaoOpcao::class, 'id_questao');
    }

}
