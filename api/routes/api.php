<?php

use App\Acesso\AreaProtegida;
use App\Acesso\CriarConta;
use App\Acesso\Entrar;
use App\Acesso\Sair;
use App\Acesso\SessaoAtual;
use App\Algoritmo\LerManutencao;
use App\Objetivo\GuardarMeta;
use App\Objetivo\LerMeta;
use App\Perfil\GuardarCorpo;
use App\Perfil\GuardarRotina;
use App\Perfil\LerCorpo;
use App\Perfil\LerRotina;
use App\RegistroDeExercicio\GuardarSessao;
use App\RegistroDeExercicio\LerModalidades;
use App\RegistroDeExercicio\LerSessoes;
use Illuminate\Support\Facades\Route;

Route::post('/acesso/conta', CriarConta::class);
Route::post('/acesso/sessao', Entrar::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/acesso/sessao', SessaoAtual::class);
    Route::delete('/acesso/sessao', Sair::class);
    Route::get('/perfil', LerCorpo::class);
    Route::put('/perfil', GuardarCorpo::class);
    Route::get('/perfil/rotina', LerRotina::class);
    Route::put('/perfil/rotina', GuardarRotina::class);
    Route::get('/objetivo', LerMeta::class);
    Route::put('/objetivo', GuardarMeta::class);
    Route::get('/manutencao', LerManutencao::class);
    Route::get('/treino/modalidades', LerModalidades::class);
    Route::get('/treino/sessoes', LerSessoes::class);
    Route::post('/treino/sessoes', GuardarSessao::class);
    Route::get('/treino', AreaProtegida::class);
    Route::get('/comida', AreaProtegida::class);
    Route::get('/alertas', AreaProtegida::class);
});
