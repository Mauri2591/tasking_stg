#!/bin/bash

DOMAIN="$1"

if [ -z "$DOMAIN" ]; then
  echo "[ERROR] Dominio no recibido"
  exit 1
fi

response=$(curl -s --max-time 20 "https://crt.sh/?q=%25.${DOMAIN}&output=json")

# Validar que sea JSON
if ! echo "$response" | jq empty >/dev/null 2>&1; then
  echo "[WARN] crt.sh no devolvió JSON válido para $DOMAIN"
  exit 0
fi

echo "$response" \
| jq -r '.[].name_value' \
| sed 's/\*\.//g' \
| tr '\n' ',' \
| tr ',' '\n' \
| sort -u
