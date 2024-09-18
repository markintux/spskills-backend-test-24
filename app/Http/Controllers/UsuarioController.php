<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    
    public function cadastrar(Request $request)
    {
        try {
            $usuario = Usuario::create($request->all());
            return response()->json(['mensagem' => 'Cadastro realizado com sucesso', 'usuario' => $usuario], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro ao cadastrar, verifique os dados'], 422);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'senha' => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['mensagem' => 'Dados incorretos'], 401);
        }

        $usuario = Usuario::where('username', $request->input('username'))->first();

        if (!$usuario || !Hash::check($request->input('senha'), $usuario->senha)) {
            return response()->json(['mensagem' => 'Dados incorretos'], 401);
        }

        $token = md5(Str::random(60) . time());

        $usuario->update(['token' => $token]);

        return response()->json(['token' => $token], 200);
    }


    public function logout(Request $request)
    {
        $token = $request->input('token');
        
        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário inválido'], 401);
        }

        $usuario->update(['token' => null]);

        return response()->json(['mensagem' => 'Logout realizado com sucesso'], 200);
    }


}
