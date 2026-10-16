<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUpTheTestEnvironment(): void
    {
        if (! $this->app) {
            $this->refreshApplication();
            $this->recusarBancoLocal();
        }

        parent::setUpTheTestEnvironment();
    }

    private function recusarBancoLocal(): void
    {
        $conexao = config('database.default');
        $banco = config('database.connections.'.$conexao.'.database');

        if ($banco !== 'bodyevolve_test') {
            throw new RuntimeException('O Pest só migra o banco bodyevolve_test.');
        }
    }

    public function soltarUsuarioDaSessao(): void
    {
        // O processo do teste reaproveita os guards. Cada request real começa sem esse cache.
        $auth = $this->app['auth'];

        foreach (['web', 'sanctum'] as $nome) {
            $guard = $auth->guard($nome);
            $usuario = new \ReflectionProperty($guard, 'user');
            $usuario->setValue($guard, null);

            if ((new \ReflectionClass($guard))->hasProperty('loggedOut')) {
                $saiu = new \ReflectionProperty($guard, 'loggedOut');
                $saiu->setValue($guard, false);
            }
        }

        $this->app['session']->driver()->flush();
    }
}
