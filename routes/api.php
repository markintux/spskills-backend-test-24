<?php

use App\Http\Controllers\ProvaController;
use App\Http\Controllers\QuestaoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/registro', [UsuarioController::class, 'cadastrar']);
Route::post('/login', [UsuarioController::class, 'login']);
Route::get('/logout', [UsuarioController::class, 'logout']);
Route::post('/banco_questoes', [QuestaoController::class, 'cadastrar']);
Route::post('/prova', [ProvaController::class, 'gerarProva']);
Route::post('/prova/responder', [ProvaController::class, 'responderQuestao']);
Route::post('/prova/finalizar', [ProvaController::class, 'finalizarProva']);
Route::post('/prova/consultar', [ProvaController::class, 'consultarProva']);
Route::get('/metricas', [ProvaController::class, 'obterEstatisticas']);