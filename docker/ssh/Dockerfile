# Vulnerable SSH Server — ShopMe CTF Lab / Secção G
#
# ┌─ CREDENCIAIS (todas da wordlist rockyou) ──────────────────────┐
# │  root      → toor          (rockyou rank ~#2000)               │
# │  admin     → admin123      (rockyou rank ~#100)                │
# │  developer → password      (rockyou rank #2)                   │
# │  backup    → 123456        (rockyou rank #1)                   │
# │  shopme    → iloveyou      (rockyou rank ~#15)                 │
# └────────────────────────────────────────────────────────────────┘
FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    openssh-server \
    curl wget netcat-openbsd nano \
    && rm -rf /var/lib/apt/lists/*

# ── Users — passwords all exist in /usr/share/wordlists/rockyou.txt ──
RUN echo 'root:toor' | chpasswd
RUN (id admin >/dev/null 2>&1 || useradd -m -s /bin/bash admin)         && echo 'admin:admin123'  | chpasswd
RUN (id developer >/dev/null 2>&1 || useradd -m -s /bin/bash developer) && echo 'developer:password' | chpasswd
RUN (id backup >/dev/null 2>&1 || useradd -m -s /bin/bash backup || usermod -s /bin/bash backup) && echo 'backup:123456' | chpasswd
RUN (id shopme >/dev/null 2>&1 || useradd -m -s /bin/bash shopme)       && echo 'shopme:iloveyou' | chpasswd

# ── Vulnerable sshd_config ───────────────────────────────────────────
RUN mkdir -p /var/run/sshd && \
    echo "PermitRootLogin yes"              >> /etc/ssh/sshd_config && \
    echo "PasswordAuthentication yes"       >> /etc/ssh/sshd_config && \
    echo "PermitEmptyPasswords no"          >> /etc/ssh/sshd_config && \
    echo "MaxAuthTries 100"                 >> /etc/ssh/sshd_config && \
    echo "X11Forwarding yes"               >> /etc/ssh/sshd_config && \
    echo "Ciphers aes128-cbc,3des-cbc,aes256-cbc,aes128-ctr,aes256-ctr" >> /etc/ssh/sshd_config && \
    echo "MACs hmac-md5,hmac-sha1,hmac-sha2-256" >> /etc/ssh/sshd_config && \
    echo "KexAlgorithms diffie-hellman-group1-sha1,diffie-hellman-group14-sha1,ecdh-sha2-nistp256" >> /etc/ssh/sshd_config

# ── CTF Flags & planted secrets ──────────────────────────────────────
#   FLAG G1: Root login — easiest, found in /root/
#   FLAG G2: Credential file — found via lateral movement in /opt/
#   FLAG G3: Deep escalation — hidden in backup user home

RUN mkdir -p /opt/shopme /root /home/admin /home/backup /home/developer /home/shopme

# root — primary flag (brute force root:toor → cat /root/flag.txt)
RUN echo 'FLAG{G1_ssh_r00t_pwned_with_toor}' > /root/flag.txt && \
    chmod 600 /root/flag.txt

# /opt/shopme — secondary flag (any user can read after login)
RUN echo '[ShopMe Internal Config]'              > /opt/shopme/config.env && \
    echo 'DB_HOST=shopme_db'                    >> /opt/shopme/config.env && \
    echo 'DB_USER=shopme_user'                  >> /opt/shopme/config.env && \
    echo 'DB_PASS=shopme_pass'                  >> /opt/shopme/config.env && \
    echo 'JWT_SECRET=shopme_jwt_secret_key_2026' >> /opt/shopme/config.env && \
    echo 'MINIO_ACCESS=minioadmin'              >> /opt/shopme/config.env && \
    echo 'MINIO_SECRET=minioadmin'              >> /opt/shopme/config.env && \
    echo 'FLAG{G2_ssh_config_exfiltrated}'      >> /opt/shopme/config.env && \
    chmod 644 /opt/shopme/config.env

# backup user — hidden flag (login as backup:123456 → find flag)
RUN echo 'FLAG{G3_ssh_backup_user_123456}'  > /home/backup/.flag && \
    echo '# ShopMe DB Backup Script'        > /home/backup/backup.sh && \
    echo 'mysqldump -h shopme_db -u shopme_user -pshopme_pass shopme_db > /tmp/dump.sql' >> /home/backup/backup.sh && \
    chmod 600 /home/backup/.flag && \
    chown backup:backup /home/backup/.flag /home/backup/backup.sh

# developer — SSH private key hint
RUN echo 'FLAG{G4_ssh_developer_password_in_rockyou}' > /home/developer/note.txt && \
    echo 'TODO: rotate all service passwords (all from rockyou.txt - BAD!)'    >> /home/developer/note.txt && \
    chown developer:developer /home/developer/note.txt

EXPOSE 22
CMD ["/usr/sbin/sshd", "-D", "-e"]
