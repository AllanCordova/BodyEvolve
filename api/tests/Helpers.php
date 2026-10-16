<?php

use Illuminate\Testing\TestResponse;

function comoCliente(): void
{
    test()->withCredentials();
    test()->withHeader('Origin', 'http://localhost:5173');
    test()->withHeader('Referer', 'http://localhost:5173/');
    test()->withHeader('Accept', 'application/json');
}

function guardarCookies(TestResponse $resposta): TestResponse
{
    foreach ($resposta->headers->getCookies() as $cookie) {
        test()->withUnencryptedCookie($cookie->getName(), $cookie->getValue());
    }

    test()->soltarUsuarioDaSessao();

    return $resposta;
}
