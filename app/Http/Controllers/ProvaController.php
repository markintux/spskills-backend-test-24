<?php

namespace App\Http\Controllers;

use App\Models\Prova;
use App\Models\Questao;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProvaController extends Controller
{
    
    public function gerarProva(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'dificuldade' => 'required|string',
            'quantidade_questoes' => 'required|integer|min:1',
            'materias' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['erro' => 'Erro ao gerar a prova', 'mensagem' => $validator->errors()], 422);
        }

        $usuario = Usuario::where('token', $request->token)->first();

        if (!$usuario || !$usuario->token) {
            return response()->json(['mensagem' => 'Usuário inválido.'], 401);
        }

        $questoes = Questao::whereIn('materia', $request->materias)
            ->where('dificuldade', $request->dificuldade)
            ->inRandomOrder()
            ->limit($request->quantidade_questoes)
            ->get();

        if ($questoes->count() < $request->quantidade_questoes) {
            return response()->json(['mensagem' => 'Número insuficiente de questões para os parâmetros fornecidos.'], 422);
        }

        $questoesArray = [];
        foreach ($questoes as $questao) {
            $opcoes = $questao->opcoes()->get()->map(function ($opcao) {
                return [
                    'letra' => $opcao->letra,
                    'opcao' => $opcao->opcao,
                    'opcao_correta' => $opcao->opcao_correta,
                ];
            })->toArray();

            $questoesArray[] = [
                'id' => $questao->id,
                'enunciado' => $questao->enunciado,
                'materia' => $questao->materia,
                'dificuldade' => $questao->dificuldade,
                'opcoes' => $opcoes,
            ];
        }

        $prova = Prova::create([
            'questoes' => $questoesArray,
            'finalizada' => false,
            'dificuldade' => $request->dificuldade,
            'quantidade_questoes' => $request->quantidade_questoes,
            'materias' => $request->materias,
            'usuario' => $usuario->id
        ]);

        return response()->json(['mensagem' => 'Prova gerada com sucesso!', 'prova' => $prova], 200);
    }

    public function responderQuestao(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'prova_id' => 'required|integer',
            'questao_id' => 'required|integer',
            'resposta' => 'required|string|in:A,B,C,D,E',
        ]);

        if ($validator->fails()) {
            return response()->json(['erro' => 'Erro ao enviar a resposta', 'mensagem' => $validator->errors()], 422);
        }

        $usuario = Usuario::where('token', $request->token)->first();

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário inválido.'], 401);
        }

        $prova = Prova::where('id', $request->prova_id)->where('usuario', $usuario->id)->first();

        if (!$prova) {
            return response()->json(['mensagem' => 'Prova não encontrada ou não pertence ao usuário.'], 404);
        }

        if ($prova->finalizada) {
            return response()->json(['mensagem' => 'Essa prova já foi finalizada.'], 422);
        }

        $questoes = collect($prova->questoes);
        $questaoIndex = $questoes->search(function ($questao) use ($request) {
            return $questao['id'] == $request->questao_id;
        });

        if ($questaoIndex === false) {
            return response()->json(['mensagem' => 'Questão não encontrada na prova.'], 404);
        }

        if (isset($questoes[$questaoIndex]['resposta_usuario'])) {
            return response()->json(['mensagem' => 'Questão já foi respondida.'], 422);
        }

        $questao = $questoes[$questaoIndex];
        $questao['resposta_usuario'] = $request->resposta;

        $questoes->put($questaoIndex, $questao);

        $prova->questoes = $questoes->toArray();
        $prova->save();

        return response()->json(['mensagem' => 'Resposta registrada com sucesso!', 'prova' => $prova], 200);
    }

    public function finalizarProva(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'prova_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['erro' => 'Erro ao finalizar a prova', 'mensagem' => $validator->errors()], 422);
        }

        $usuario = Usuario::where('token', $request->token)->first();

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário inválido.'], 401);
        }

        $prova = Prova::where('id', $request->prova_id)->where('usuario', $usuario->id)->first();

        if (!$prova) {
            return response()->json(['mensagem' => 'Prova não encontrada ou não pertence ao usuário.'], 404);
        }

        if ($prova->finalizada) {
            return response()->json(['mensagem' => 'Prova já está finalizada.'], 422);
        }

        $prova->finalizada = true;
        $prova->save();

        return response()->json(['mensagem' => 'Prova finalizada com sucesso!'], 200);
    }

    public function consultarProva(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'prova_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['erro' => 'Erro ao consultar a prova', 'mensagem' => $validator->errors()], 422);
        }

        $usuario = Usuario::where('token', $request->token)->first();

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário inválido.'], 401);
        }

        $prova = Prova::where('id', $request->prova_id)
                    ->where('usuario', $usuario->id)
                    ->first();

        if (!$prova) {
            return response()->json(['mensagem' => 'Prova não encontrada ou não pertence ao usuário.'], 404);
        }

        return response()->json([
            'mensagem' => 'Prova consultada com sucesso!',
            'prova' => [
                'id' => $prova->id,
                'questoes' => $prova->questoes,
                'finalizada' => $prova->finalizada,
                'dificuldade' => $prova->dificuldade,
                'quantidade_questoes' => $prova->quantidade_questoes,
                'materias' => $prova->materias,
                'usuario' => $prova->usuario
            ]
        ], 200);
    }

    public function obterEstatisticas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['erro' => 'Erro ao obter estatísticas', 'mensagem' => $validator->errors()], 422);
        }

        $usuario = Usuario::where('token', $request->token)->first();

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário inválido.'], 401);
        }

        $provas = Prova::where('usuario', $usuario->id)->where('finalizada', true)->get();

        $totalProvas = $provas->count();
        $totalQuestoesCorretas = 0;
        $totalQuestoesErradas = 0;
        $questoesCorretasPorMateria = [];
        $questoesErradasPorMateria = [];

        foreach ($provas as $prova) {

            $questoes = is_array($prova->questoes) ? $prova->questoes : json_decode($prova->questoes, true);

            foreach ($questoes as $questao) {

                $respostaUsuario = $questao['resposta_usuario'] ?? null;
                $opcaoCorreta = null;

                if (isset($questao['opcoes']) && is_array($questao['opcoes'])) {
                    foreach ($questao['opcoes'] as $opcao) {
                        if (isset($opcao['opcao_correta']) && $opcao['opcao_correta'] == 1) {
                            $opcaoCorreta = $opcao['letra'];
                            break;
                        }
                    }
                }

                if ($respostaUsuario && $opcaoCorreta) {
                    if ($respostaUsuario === $opcaoCorreta) {
                        $totalQuestoesCorretas++;
                        if (isset($questoesCorretasPorMateria[$questao['materia']])) {
                            $questoesCorretasPorMateria[$questao['materia']]++;
                        } else {
                            $questoesCorretasPorMateria[$questao['materia']] = 1;
                        }
                    } else {
                        $totalQuestoesErradas++;
                        if (isset($questoesErradasPorMateria[$questao['materia']])) {
                            $questoesErradasPorMateria[$questao['materia']]++;
                        } else {
                            $questoesErradasPorMateria[$questao['materia']] = 1;
                        }
                    }
                }
            }
        }

        return response()->json([
            'total_provas_realizadas' => $totalProvas,
            'total_questoes_corretas' => $totalQuestoesCorretas,
            'total_questoes_erradas' => $totalQuestoesErradas,
            'questoes_corretas_por_materia' => $questoesCorretasPorMateria,
            'questoes_erradas_por_materia' => $questoesErradasPorMateria
        ], 200);
    }

}