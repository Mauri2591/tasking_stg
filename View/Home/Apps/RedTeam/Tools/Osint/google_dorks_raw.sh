#!/bin/bash

DOMAIN="$1"
DATE="$(date -u)"

if [ -z "$DOMAIN" ]; then
  echo "[ERROR] Dominio no recibido"
  exit 1
fi

echo "# =============================================="
echo "# OSINT PASIVO - Wayback Files"
echo "# Dominio: $DOMAIN"
echo "# Fecha: $DATE"
echo "# Fuente: Internet Archive (CDX)"
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
  echo "[+] Wayback search: /$f"

  curl -s \
    --max-time 15 \
    --connect-timeout 5 \
    --retry 1 \
    --retry-delay 2 \
    "https://web.archive.org/cdx/search/cdx?url=$DOMAIN/$f&output=json" \
  | jq -c '.[]?' 2>/dev/null \
  | grep -v urlkey \
  | sed "s/^/  FOUND: /"

  echo
done

exit 0
