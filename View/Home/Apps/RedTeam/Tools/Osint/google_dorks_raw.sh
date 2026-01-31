#!/bin/bash

DOMAIN="$1"
DATE="$(date -u)"

echo "# =============================================="
echo "# OSINT Google Dorks + Wayback"
echo "# Dominio: $DOMAIN"
echo "# Fecha: $DATE"
echo "# Fuente: Internet Archive (pasivo)"
echo "# =============================================="
echo

FILES=(
  ".env"
  ".git/config"
  "config.php"
  "config.yml"
  "backup.zip"
  "database.sql"
)

for f in "${FILES[@]}"; do
  echo "[+] Buscando historial de: /$f"
  curl -s "https://web.archive.org/cdx/search/cdx?url=$DOMAIN/$f&output=json" \
    | jq -c '.[]' 2>/dev/null \
    | grep -v urlkey \
    | sed "s/^/  FOUND: /"
  echo
done
