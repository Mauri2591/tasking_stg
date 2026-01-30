#!/bin/bash
# ------------------------------------------------------------
# OSINT PASIVO - Enumeración de subdominios vía crt.sh
# Soporta múltiples dominios
# ------------------------------------------------------------

if [ "$#" -lt 1 ]; then
  echo "[ERROR] No se recibieron dominios"
  exit 2
fi

for ACTIVO in "$@"; do

  echo "===== $ACTIVO ====="

  URL="https://crt.sh/?q=%25.${ACTIVO}&output=json"

  response=$(curl -s --max-time 30 -A "Mozilla/5.0" "$URL")

  if [ -z "$response" ]; then
    echo "[WARN] crt.sh no devolvió contenido para: $ACTIVO"
    continue
  fi

  results=$(echo "$response" \
    | sed 's/\\n/\n/g' \
    | grep -oE "([a-zA-Z0-9_-]+\.)+${ACTIVO//./\\.}" \
    | sort -u)

  if [ -z "$results" ]; then
    echo "[INFO] Sin subdominios parseables desde crt.sh"
    continue
  fi

  echo "$results"
done

exit 0
