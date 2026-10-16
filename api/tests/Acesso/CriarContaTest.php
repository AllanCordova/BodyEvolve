<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    comoCliente();
});

it('grava a conta e guarda a senha só como hash', function () {
    $resposta = guardarCookies(test()->postJson('/api/acesso/conta', conta()));

    $resposta->assertCreated();

    $usuario = User::query()->where('email', 'ana@example.com')->first();

    expect($usuario)->not->toBeNull();
    expect($usuario->name)->toBe('Ana');
    expect($usuario->icon_path)->toBeNull();
    expect($usuario->password)->not->toBe('senha-segura');
    expect(Hash::check('senha-segura', $usuario->password))->toBeTrue();

    $conta = [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'icon_url' => null,
    ];

    $resposta->assertExactJson($conta);

    test()->getJson('/api/acesso/sessao')
        ->assertOk()
        ->assertExactJson($conta);
});

it('recusa e-mail duplicado e senha curta', function () {
    test()->postJson('/api/acesso/conta', conta())->assertCreated();

    test()->postJson('/api/acesso/conta', conta([
        'email' => 'Ana@Example.com',
    ]))->assertUnprocessable()->assertJsonValidationErrors('email');

    test()->postJson('/api/acesso/conta', conta([
        'email' => 'outra@example.com',
        'password' => 'curta',
    ]))->assertUnprocessable()->assertJsonValidationErrors('password');

    expect(User::query()->count())->toBe(1);
});

function conta(array $dados = []): array
{
    return [
        'name' => 'Ana',
        'email' => 'Ana@Example.com',
        'password' => 'senha-segura',
        ...$dados,
    ];
}
