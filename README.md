# BodyEvolve

O BodyEvolve alinha comida e treino a uma meta só: hipertrofia, emagrecimento, força ou resistência. A pessoa registra o dia. O app avisa quando a ingestão ou o estímulo saiu da rota.

O código ainda não existe. Os comandos abaixo são o jeito previsto de subir o que a [arquitetura](docs/system/architecture.md) descreve.

## Rodar

Banco, para desenvolver a API na máquina:

```bash
docker compose -f compose.local.yml up -d
```

Cliente, API e banco juntos:

```bash
docker compose -f compose.prod.yml up --build
```

Cliente em `http://localhost:5173`. API em `http://localhost:8000`.
