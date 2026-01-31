#!/bin/bash
# ------------------------------------------------------------
# OSINT PASIVO - crt.sh RAW con retry y backoff
# ------------------------------------------------------------
set -euo pipefail

ACTIVO="${1:-}"

if [[ -z "$ACTIVO" ]]; then
  echo "[ERROR] Dominio no recibido"
  exit 2
fi

# Validación mínima de dominio
if [[ "$ACTIVO" != *.* ]]; then
  echo "[WARN] Activo sin TLD válido: $ACTIVO"
  exit 0
fi

URL="https://crt.sh/?q=%25.${ACTIVO}&output=json"
MAX_RETRIES=3
DELAY=3
response=""

for ((i=1; i<=MAX_RETRIES; i++)); do

  response="$(curl -sS \
    --http1.1 \
    --connect-timeout 5 \
    --max-time 40 \
    -A "Mozilla/5.0" \
    "$URL" || true)"

  # Respuesta válida (no vacía y no 502)
  if [[ -n "$response" ]] && [[ "$response" != *"502 Bad Gateway"* ]]; then
    echo "$response"
    exit 0
  fi

  # Si falló, esperamos y reintentamos (esto va a stderr para no “ensuciar” output)
  echo "[INFO] Intento $i/$MAX_RETRIES fallido para $ACTIVO, reintentando..." >&2
  sleep "$DELAY"
done

echo "[WARN] crt.sh no respondió correctamente tras $MAX_RETRIES intentos para $ACTIVO"
exit 3
