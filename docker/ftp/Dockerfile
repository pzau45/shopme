# Vulnerable FTP Server (vsftpd) — ShopMe CTF Lab / Secção G
#
# ┌─ CREDENCIAIS (todas da wordlist rockyou) ──────────────────────┐
# │  anonymous → (sem password)  [login anónimo habilitado]         │
# │  ftpuser   → password        (rockyou rank #2)                  │
# │  uploads   → 123456          (rockyou rank #1)                  │
# │  ftpadmin  → letmein         (rockyou rank ~#10)                │
# └────────────────────────────────────────────────────────────────┘
FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    vsftpd ftp \
    && rm -rf /var/lib/apt/lists/*

# ── Users — passwords all exist in /usr/share/wordlists/rockyou.txt ──
RUN useradd -m -s /bin/bash ftpuser  && echo 'ftpuser:password'  | chpasswd
RUN useradd -m -s /bin/bash uploads  && echo 'uploads:123456'    | chpasswd
RUN useradd -m -s /bin/bash ftpadmin && echo 'ftpadmin:letmein'  | chpasswd

# ── Anonymous FTP root ────────────────────────────────────────────────
RUN mkdir -p /srv/ftp/public /srv/ftp/uploads && \
    chmod 755 /srv/ftp && \
    chmod 777 /srv/ftp/public /srv/ftp/uploads && \
    chown nobody:nogroup /srv/ftp

# ── CTF Flags & planted secrets ──────────────────────────────────────
#   FLAG G5: Anonymous access — no credentials needed
#   FLAG G6: Authenticated user — brute force ftpuser:password
#   FLAG G7: Admin user — deeper access, ftpadmin:letmein

# Public dir (accessible anonymous — easiest)
RUN echo 'FLAG{G5_ftp_anonymous_login_no_password_needed}'   > /srv/ftp/public/flag.txt && \
    echo ''                                                 >> /srv/ftp/public/flag.txt && \
    echo '# ShopMe Internal FTP Server'                    >> /srv/ftp/public/flag.txt && \
    echo '# Authenticated area: use ftpuser / uploads / ftpadmin' >> /srv/ftp/public/flag.txt

RUN echo 'DB_HOST=shopme_db:3306'                            > /srv/ftp/public/config_backup.txt && \
    echo 'DB_USER=shopme_user'                              >> /srv/ftp/public/config_backup.txt && \
    echo 'DB_PASS=shopme_pass'                              >> /srv/ftp/public/config_backup.txt && \
    echo 'MINIO=http://shopme_minio:9000 (minioadmin/minioadmin)' >> /srv/ftp/public/config_backup.txt

# ftpuser home — authenticated flag (ftpuser:password)
RUN echo 'FLAG{G6_ftp_cleartext_creds_ftpuser_password}'    > /home/ftpuser/flag.txt && \
    echo ''                                                 >> /home/ftpuser/flag.txt && \
    echo 'Credentials intercepted via Wireshark/tcpdump:'  >> /home/ftpuser/flag.txt && \
    echo '  USER ftpuser'                                  >> /home/ftpuser/flag.txt && \
    echo '  PASS password'                                 >> /home/ftpuser/flag.txt && \
    chown ftpuser:ftpuser /home/ftpuser/flag.txt

# ftpadmin home — hardest, pivot flag
RUN mkdir -p /home/ftpadmin/private && \
    echo 'FLAG{G7_ftp_admin_letmein_pivoted}'               > /home/ftpadmin/private/flag.txt && \
    echo ''                                                 >> /home/ftpadmin/private/flag.txt && \
    echo '=== ShopMe Admin FTP Credentials ==='            >> /home/ftpadmin/private/credentials.txt && \
    echo 'ftpadmin : letmein'                              >> /home/ftpadmin/private/credentials.txt && \
    echo 'ftpuser  : password'                             >> /home/ftpadmin/private/credentials.txt && \
    echo 'uploads  : 123456'                               >> /home/ftpadmin/private/credentials.txt && \
    chown -R ftpadmin:ftpadmin /home/ftpadmin/private

COPY vsftpd.conf /etc/vsftpd.conf

# Create vsftpd empty dir
RUN mkdir -p /var/run/vsftpd/empty

EXPOSE 21 21100-21110
CMD ["/usr/sbin/vsftpd", "/etc/vsftpd.conf"]
