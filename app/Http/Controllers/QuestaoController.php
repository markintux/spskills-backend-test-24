<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\QuestaoOpcao;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestaoController extends Controller
{
    public function cadastrar(Request $request)
    {
        $regras = [
            'token' => 'required',
            'enunciado' => 'required',
            'materia' => 'required',
            'dificuldade' => 'required',
            'opcoes' => 'required|array|min:5',
            'opcoes.*.letra' => 'required|in:A,B,C,D,E',
            'opcoes.*.opcao' => 'required|distinct',
            'opcoes.*.opcao_correta' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $regras);

        if ($validator->fails()) {
            return response()->json(['mensagem' => 'Erro ao cadastrar, verifique os dados','errors' => $validator->errors()], 401);
        }

        $validatedData = $validator->validated();

        $admin = Usuario::where('token', $request->input('token'))->where('username', 'admin')->first();

        if (!$admin) {
            return response()->json(['mensagem' => 'Usuário inválido'], 401);
        }

        $opcoesCorretas = array_filter($request->input('opcoes'), function ($opcao) {
            return $opcao['opcao_correta'] == true;
        });

        if (count($opcoesCorretas) !== 1) {
            return response()->json(['mensagem' => 'Erro ao cadastrar, verifique os dados'], 422);
        }

        DB::beginTransaction();

        try {

            $questao = Questao::create([
                'enunciado' => $request->input('enunciado'),
                'materia' => $request->input('materia'),
                'dificuldade' => $request->input('dificuldade')
            ]);

            foreach ($request->input('opcoes') as $opcao) {
                QuestaoOpcao::create([
                    'id_questao' => $questao->id,
                    'letra' => $opcao['letra'],
                    'opcao' => $opcao['opcao'],
                    'opcao_correta' => $opcao['opcao_correta']
                ]);
            }

            DB::commit();

            return response()->json(['mensagem' => 'Cadastro realizado com sucesso!','questao' => $questao], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['mensagem' => 'Erro ao cadastrar, verifique os dados.','error' => $e->getMessage()], 422);
        }
    }
}
