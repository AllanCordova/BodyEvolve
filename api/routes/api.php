<?php

use App\Acesso\AreaProtegida;
use App\Acesso\CriarConta;
use App\Acesso\Entrar;
use App\Acesso\Sair;
use App\Acesso\SessaoAtual;
use Illuminate\Support\Facades\Route;

Route::post('/acesso/conta', CriarConta::class);
Route::post('/acesso/sessao', Entrar::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/acesso/sessao', SessaoAtual::class);
    Route::delete('/acesso/sessao', Sair::class);
    Route::get('/perfil', AreaProtegida::class);
    Route::get('/treino', AreaProtegida::class);
    Route::get('/comida', AreaProtegida::class);
    Route::get('/alertas', AreaProtegida::class);
});
