# BodyEvolve

O BodyEvolve alinha comida e treino a uma meta só: hipertrofia, emagrecimento, força ou resistência. A pessoa registra o dia. O app avisa quando a ingestão ou o estímulo saiu da rota.

A estrutura está em [arquitetura](docs/system/architecture.md).

## Rodar

API, cliente e banco:

```bash
npm install
npm run dev
```

`npm run dev` sobe o Postgres com o Docker Compose e, em seguida, a API e o cliente. A API cria o atalho `public/storage` quando ele ainda não existe, para servir o ícone da conta. Só o banco: `npm run db`.

Banco, para desenvolver a API na máquina:

```bash
docker compose up -d
```

Na pasta `api`, com o PHP 8.5:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
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
