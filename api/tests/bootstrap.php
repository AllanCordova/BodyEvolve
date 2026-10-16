<?php

require __DIR__.'/../vendor/autoload.php';

$variaveis = [
    'DB_CONNECTION' => 'pgsql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '5432',
    'DB_DATABASE' => 'bodyevolve_test',
    'DB_USERNAME' => 'bodyevolve',
    'DB_PASSWORD' => 'bodyevolve',
    'DB_URL' => '',
];

foreach ($variaveis as $nome => $valor) {
    putenv($nome.'='.$valor);
    $_ENV[$nome] = $valor;
    $_SERVER[$nome] = $valor;
}

if (getenv('DB_DATABASE') !== 'bodyevolve_test' || ($_SERVER['DB_DATABASE'] ?? null) !== 'bodyevolve_test') {
    fwrite(STDERR, "O Pest só pode usar o banco bodyevolve_test.\n");
    exit(1);
}

$pdo = new PDO(
    'pgsql:host=127.0.0.1;port=5432;dbname=postgres',
    'bodyevolve',
    'bodyevolve',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$existe = $pdo->prepare('SELECT 1 FROM pg_database WHERE datname = ?');
$existe->execute(['bodyevolve_test']);

if ($existe->fetchColumn() === false) {
    $pdo->exec('CREATE DATABASE bodyevolve_test');
}
