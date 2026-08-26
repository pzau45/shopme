#!/bin/sh
# MinIO bucket initialization — ShopMe CTF Lab / Secção G
# Creates buckets with intentional misconfigurations and plants CTF flags

set -e

echo "[*] Waiting for MinIO to be ready..."
until curl -sf http://shopme_minio:9000/minio/health/live; do
  sleep 2
done

echo "[*] Configuring MinIO client alias..."
mc alias set shopme http://shopme_minio:9000 minioadmin minioadmin

# ── Create buckets ────────────────────────────────────────────────────
echo "[*] Creating buckets..."
mc mb --ignore-existing shopme/shopme-files
mc mb --ignore-existing shopme/shopme-backups
mc mb --ignore-existing shopme/shopme-avatars
mc mb --ignore-existing shopme/shopme-private

# ── Bucket permissions — intentionally misconfigured ─────────────────
# shopme-backups → PUBLIC (world readable) — CTF vulnerability G8
# shopme-files   → public download only
mc anonymous set public   shopme/shopme-backups
mc anonymous set download shopme/shopme-files

echo "[*] Uploading CTF flag files..."

# ── FLAG G11 — Public bucket, no auth needed ─────────────────────────
# Access: curl http://localhost:9000/shopme-backups/flag.txt
cat > /tmp/flag_g11.txt << 'EOF'
FLAG{G11_minio_public_bucket_world_readable}

This bucket is publicly readable without any authentication.
Try listing it:
  curl "http://localhost:9000/shopme-backups/?list-type=2"
  aws --endpoint-url http://localhost:9000 s3 ls s3://shopme-backups/
EOF
mc cp /tmp/flag_g11.txt shopme/shopme-backups/flag.txt

# ── FLAG G12 — Exposed user data dump ────────────────────────────────
# Access: curl http://localhost:9000/shopme-backups/users_export.csv
cat > /tmp/users_export.csv << 'EOF'
user_id,email,password_md5,plaintext_rockyou,role
1,admin@shopme.local,0192023a7bbd73250516f069df18b500,admin123,admin
2,carlos@example.com,9ad48828b0955513f7cf0f7f6510c8f8,carlos123,customer
3,ana@example.com,5f4dcc3b5aa765d61d8327deb882cf99,password,customer
4,bruno.mendes@tech.pt,e10adc3949ba59abbe56e057f20f883e,123456,customer
5,sofia.rodrigues@mail.com,9cfc2a47aad6c8784af2b2187bd6b0f4,sofia2024,customer
6,diogo.ferreira@domain.pt,d8578edf8458ce06fbc5bb76a58c5ca4,qwerty,customer
7,ines.santos@company.com,0d107d09f5bbe40cade3de5c71e9e9b7,letmein,customer
8,miguel.costa@techlab.io,f25a2fc72690b780b2a14e140ef6a9e0,iloveyou,customer
9,beatriz.lopes@web.pt,1311f9e2be693052bfbba6926e881db5,beatriz1,customer
10,tiago.martins@dev.com,4276e60016d27684654516632a530996,tiago123,customer
11,mariana.gomes@shop.pt,0571749e2ac330a7455809c6b0e7af90,sunshine,customer
12,pedro.almeida@cloud.org,d64918b527e8890afe359dc2e238b936,pedro1234,customer
FLAG{G12_minio_user_dump_md5_cracked_with_rockyou}
EOF
mc cp /tmp/users_export.csv shopme/shopme-backups/users_export.csv

# ── FLAG G13 — Exposed application config ────────────────────────────
# Access: curl http://localhost:9000/shopme-backups/config.env
cat > /tmp/config.env << 'EOF'
# ShopMe Production Config — LEAKED VIA MINIO PUBLIC BUCKET
DB_HOST=shopme_db
DB_PORT=3306
DB_NAME=shopme_db
DB_USER=shopme_user
DB_PASS=shopme_pass
JWT_SECRET=shopme_jwt_secret_key_2026
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
ADMIN_EMAIL=admin@shopme.local
ADMIN_PASS_MD5=0192023a7bbd73250516f069df18b500
SSH_ROOT_PASS=toor
FTP_USER=ftpuser
FTP_PASS=password
FLAG{G13_minio_config_env_exposed_full_takeover}
EOF
mc cp /tmp/config.env shopme/shopme-backups/config.env

# ── FLAG G14 — Private bucket (authenticated) ─────────────────────────
# Requires MinIO credentials: minioadmin:minioadmin → console at :9001
cat > /tmp/flag_g14.txt << 'EOF'
FLAG{G14_minio_console_weak_creds_minioadmin}

You accessed the MinIO admin console at http://localhost:9001
Credentials: minioadmin / minioadmin

From here you have full control over all buckets and objects.
EOF
mc cp /tmp/flag_g14.txt shopme/shopme-private/flag.txt

# ── Database dump simulation (discoverable file) ──────────────────────
cat > /tmp/db_readme.txt << 'EOF'
ShopMe Database Backup Index
============================
shopme_db_2026-08-01.sql.gz  (458 MB)
shopme_db_2026-07-01.sql.gz  (441 MB)
shopme_db_2026-06-01.sql.gz  (398 MB)

Download via:
  mc cp shopme/shopme-backups/<filename> .
  curl http://localhost:9000/shopme-backups/<filename>
EOF
mc cp /tmp/db_readme.txt shopme/shopme-backups/db_backups_index.txt

echo ""
echo "[+] MinIO CTF initialization complete!"
echo ""
echo "┌─ Public flags (no auth required) ──────────────────────────────┐"
echo "│  curl http://localhost:9000/shopme-backups/flag.txt             │"
echo "│  curl http://localhost:9000/shopme-backups/users_export.csv     │"
echo "│  curl http://localhost:9000/shopme-backups/config.env           │"
echo "│  curl \"http://localhost:9000/shopme-backups/?list-type=2\"       │"
echo "└─────────────────────────────────────────────────────────────────┘"
echo "┌─ Authenticated flag (minioadmin:minioadmin) ────────────────────┐"
echo "│  Console: http://localhost:9001                                  │"
echo "│  mc alias set pwned http://localhost:9000 minioadmin minioadmin  │"
echo "│  mc cp pwned/shopme-private/flag.txt .                          │"
echo "└─────────────────────────────────────────────────────────────────┘"
mc ls shopme/
