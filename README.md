# BodyEvolve

O BodyEvolve alinha comida e treino a uma meta só: hipertrofia, emagrecimento, força ou resistência. A pessoa registra o dia. O app avisa quando a ingestão ou o estímulo saiu da rota.

A estrutura está em [arquitetura](docs/system/architecture.md).

## Rodar

Banco, para desenvolver a API na máquina:

```bash
docker compose -f compose.local.yml up -d
```

Na pasta `api`, com o PHP 8.5:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Na pasta `client`, com o Node 24:

```bash
npm ci
npm run dev
```

Cliente, API e banco juntos:

```bash
docker compose -f compose.prod.yml up --build
```

Cliente em `http://localhost:5173`. API em `http://localhost:8000`.
