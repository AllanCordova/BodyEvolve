<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
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
