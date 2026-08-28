# Deploy de produção

> Este repositório foi criado como laboratório de segurança e contém vulnerabilidades graves na camada da aplicação. O stack abaixo endurece a infraestrutura, mas **não torna o código seguro para exposição pública**. Corrija e teste as vulnerabilidades documentadas em `VULNS.md` antes de operar com dados reais.

## Preparação

1. Configure um registo DNS `A` para o domínio apontar para o IP da VPS e libere as portas TCP 80 e 443 no firewall.
2. Copie o modelo de ambiente e preencha todos os valores com segredos únicos:

   ```bash
   cp .env.production.example .env.production
   chmod 600 .env.production
   ```

3. Gere segredos, por exemplo:

   ```bash
   openssl rand -hex 32
   ```

4. Suba somente o stack de produção:

   ```bash
   docker compose -f docker-compose.production.yml --env-file .env.production up -d --build
   ```

5. Verifique o estado:

   ```bash
   docker compose -f docker-compose.production.yml --env-file .env.production ps
   ```

O Caddy solicita e renova automaticamente o certificado TLS para `APP_DOMAIN`. O MySQL não publica qualquer porta no host; somente os containers da rede interna podem acessá-lo.

## Operação

- Faça backup regular do volume `db_data` antes de qualquer atualização.
- Nunca execute o `docker-compose.yml` de laboratório na VPS pública: ele inclui serviços deliberadamente inseguros.
- Não versione `.env.production`, certificados ou backups.
