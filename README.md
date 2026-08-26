# ShopMe — Modern E-Commerce Platform (Educational Pentest Laboratory)

**ShopMe** é uma aplicação web moderna de e-commerce desenvolvida em PHP 8+ seguindo o padrão de autoloading **PSR-4**, concebida para servir como um laboratório prático de auditoria de segurança e testes de penetração (pentesting).

A aplicação simula um ecossistema completo de loja online — catálogo de produtos, carrinho de compras, checkout com cupões, perfil de utilizador, sistema de suporte/tickets e painel de administração corporativo.

---

## Architecture & Technology Stack

- **Backend**: PHP 8.2 FPM (Pure PHP / Custom PSR-4 Router)
- **Web Server**: Nginx (Reverse Proxy & FastCGI handler)
- **Database**: MySQL 8.0 (Relational schema with PDO / mysqli data layer)
- **Frontend**: Custom Modern Dark Mode UI (Vanilla CSS, Glassmorphism, Vanilla JS)
- **Containerization**: Docker & Docker Compose

---

## Quick Start & Setup Guide

### Pré-requisitos
- Docker (v20.10 ou superior)
- Docker Compose (v2.0 ou superior)

### Subir o Ambiente
1. Clone ou navegue até à pasta raiz do projeto:
   ```bash
   cd /caminho/para/shopme
   ```

2. Inicie os pacotes Docker via Docker Compose:
   ```bash
   docker compose up -d --build
   ```

3. Verifique o estado dos contentores:
   ```bash
   docker compose ps
   ```

4. Aceda à aplicação no seu browser:
   [http://localhost:8080](http://localhost:8080)

---

## Contas de Teste Pré-configuradas

A base de dados é automaticamente inicializada com dados de teste. Pode utilizar as seguintes credenciais:

| Perfil | Email | Palavra-passe | Papel |
|---|---|---|---|
| **Administrador** | `admin@shopme.local` | `admin123` | Acesso total ao Painel Admin |
| **Cliente 1** | `carlos@example.com` | `user123` | Compras, Perfil, Histórico |
| **Cliente 2** | `ana@example.com` | `user123` | Compras, Perfil, Histórico |

---

## Reiniciar / Repor o Ambiente de Laboratório

Para restaurar a aplicação e a base de dados ao estado inicial limpo (eliminando uploads, novas contas ou modificações na base de dados):

```bash
# Parar e remover contentores e volumes
docker compose down -v

# Reiniciar ambiente limpo
docker compose up -d
```

---

## Estrutura do Projeto

```
/
├── composer.json           # Definição PSR-4 (App\ -> src/App/)
├── docker-compose.yml       # Orquestração (Nginx, PHP-FPM, MySQL)
├── Dockerfile               # Imagem PHP 8.2-FPM com extensões MySQL/GD
├── nginx.conf               # Configuração do web server
├── database/
│   ├── schema.sql           # Estrutura de tabelas MySQL
│   └── seed.sql             # Dados iniciais realista
├── public/                  # Document Root público
│   ├── index.php            # Front Controller & Rotas
│   ├── static/              # CSS & JS estáticos
│   └── uploads/             # Ficheiros de utilizador
├── src/App/                 # Código-fonte PHP (PSR-4)
│   ├── Config/
│   ├── Controllers/
│   ├── Models/
│   ├── Middleware/
│   └── Services/
└── views/                   # Templates de visualização (HTML/PHP)
```

---

## ⚖️ Aviso Ético e Legal (Ethical Disclaimer)

> [!CAUTION]
> **USO EXCLUSIVAMENTE EDUCACIONAL E LOCAL**
> 
> Esta aplicação foi desenvolvida **exclusivamente para fins educacionais, treino de equipas de segurança cibernética e auditoria defensiva/ofensiva em ambientes isolados**.
> 
> - **NÃO publique** este software num servidor exposto à internet pública.
> - **NÃO utilize** as técnicas exercitadas neste ambiente contra sistemas ou redes de terceiros sem autorização prévia por escrito.
> - O utilizador assume total responsabilidade pelo uso adequado e ético deste ambiente de laboratório.
