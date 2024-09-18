<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestaoOpcao extends Model
{
    use HasFactory;

    protected $table = 'questao_opcoes';

    protected $fillable = ['id_questao','letra','opcao','opcao_correta'];

    public function questao()
    {
        return $this->belongsTo(Questao::class, 'id_questao');
    }

}
