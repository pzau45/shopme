# Vulnerable Telnet Server — ShopMe CTF Lab / Secção G
#
# ┌─ CREDENCIAIS (todas da wordlist rockyou) ──────────────────────┐
# │  root      → toor          (rockyou rank ~#2000)               │
# │  admin     → admin123      (rockyou rank ~#100)                 │
# │  guest     → guest         (rockyou rank ~#50)                  │
# │  operator  → sunshine      (rockyou rank ~#25)                  │
# │  monitor   → qwerty        (rockyou rank ~#5)                   │
# └────────────────────────────────────────────────────────────────┘
FROM alpine:3.18

RUN apk add --no-cache \
    busybox-extras \
    shadow \
    bash \
    curl \
    wget

# ── Users — passwords all exist in /usr/share/wordlists/rockyou.txt ──
RUN echo 'root:toor'       | chpasswd
RUN (id admin >/dev/null 2>&1 || adduser -D -s /bin/sh admin)       && echo 'admin:admin123'  | chpasswd
RUN (id guest >/dev/null 2>&1 || adduser -D -s /bin/sh guest)       && echo 'guest:guest'     | chpasswd
RUN (id operator >/dev/null 2>&1 || adduser -D -s /bin/sh operator) && echo 'operator:sunshine' | chpasswd
RUN (id monitor >/dev/null 2>&1 || adduser -D -s /bin/sh monitor)   && echo 'monitor:qwerty'  | chpasswd

# ── CTF Flags & planted secrets ──────────────────────────────────────
#   FLAG G8:  Guest login — easiest, found immediately after login
#   FLAG G9:  Admin login — medium, sniff credentials then login
#   FLAG G10: Root login — hardest, requires brute force root:toor

RUN mkdir -p /opt/shopme /root /home/admin /home/guest /home/operator /home/monitor

# MOTD — leaks internal network map (intentional vulnerability G6)
RUN printf '╔════════════════════════════════════════════╗\n' > /etc/motd && \
    printf '║     ShopMe Internal Network — Telnet       ║\n' >> /etc/motd && \
    printf '╠════════════════════════════════════════════╣\n' >> /etc/motd && \
    printf '║  Internal Services (network: shopme_net):  ║\n' >> /etc/motd && \
    printf '║  DB      → shopme_db:3306                  ║\n' >> /etc/motd && \
    printf '║           user: shopme_user / shopme_pass  ║\n' >> /etc/motd && \
    printf '║  FTP     → shopme_ftp:21                   ║\n' >> /etc/motd && \
    printf '║           user: ftpuser / password         ║\n' >> /etc/motd && \
    printf '║  MinIO   → shopme_minio:9000               ║\n' >> /etc/motd && \
    printf '║           user: minioadmin / minioadmin    ║\n' >> /etc/motd && \
    printf '║  Web App → http://shopme_web:80            ║\n' >> /etc/motd && \
    printf '╚════════════════════════════════════════════╝\n' >> /etc/motd

# guest — FLAG G8 (easiest: connect as guest/guest → ls → cat flag.txt)
RUN echo 'FLAG{G8_telnet_guest_login_cleartext_protocol}'   > /home/guest/flag.txt && \
    echo ''                                                 >> /home/guest/flag.txt && \
    echo 'Hint: sniff this session with Wireshark!'        >> /home/guest/flag.txt && \
    echo '  Filter: tcp.port == 23'                        >> /home/guest/flag.txt && \
    chmod 644 /home/guest/flag.txt

# admin — FLAG G9 (medium: sniff admin:admin123 login → cat .flag)
RUN echo 'FLAG{G9_telnet_admin_admin123_sniffed}'           > /home/admin/.flag && \
    echo ''                                                 >> /home/admin/.flag && \
    printf '=== Admin Notes ===\n'                         >> /home/admin/.flag && \
    printf 'Web admin credentials: admin@shopme.local / admin123\n' >> /home/admin/.flag && \
    printf 'DB root: rootpassword123\n'                    >> /home/admin/.flag && \
    chmod 644 /home/admin/.flag

# root — FLAG G10 (hardest: brute force root:toor → cat /root/flag.txt)
RUN echo 'FLAG{G10_telnet_root_toor_system_owned}'          > /root/flag.txt && \
    echo ''                                                 >> /root/flag.txt && \
    echo '=== Full System Compromise ==='                  >> /root/flag.txt && \
    echo 'You have root access via Telnet.'                >> /root/flag.txt && \
    echo 'All internal network credentials are exposed.'   >> /root/flag.txt

# operator — bonus flag hidden in /opt/shopme
RUN echo 'FLAG{G10b_telnet_operator_sunshine_bonus}'        > /opt/shopme/secret.txt && \
    echo 'MINIO_ACCESS_KEY=minioadmin'                     >> /opt/shopme/secret.txt && \
    echo 'MINIO_SECRET_KEY=minioadmin'                     >> /opt/shopme/secret.txt && \
    echo 'BUCKET=shopme-backups (public read!)'            >> /opt/shopme/secret.txt && \
    chmod 644 /opt/shopme/secret.txt

# Telnet runs on port 23
EXPOSE 23
CMD ["/usr/sbin/telnetd", "-l", "/bin/login", "-F"]
