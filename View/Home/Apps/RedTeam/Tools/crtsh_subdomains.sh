#!/bin/bash
# ------------------------------------------------------------
# OSINT PASIVO - Enumeración de subdominios vía crt.sh
# Robusto contra JSON inválido / rate-limit / dominios grandes
# ------------------------------------------------------------

ACTIVO="$1"

if [ -z "$ACTIVO" ]; then
  echo "[ERROR] Dominio no recibido"
  exit 2
fi

URL="https://crt.sh/?q=%25.${ACTIVO}&output=json"

# User-Agent real (CLAVE)
response=$(curl -s --max-time 30 -A "Mozilla/5.0" "$URL")

# Si no hubo respuesta
if [ -z "$response" ]; then
  echo "[WARN] crt.sh no devolvió contenido para: $ACTIVO"
  exit 3
fi

# Extraer subdominios SIN confiar en JSON
results=$(echo "$response" \
  | sed 's/\\n/\n/g' \
  | grep -oE "([a-zA-Z0-9_-]+\.)+${ACTIVO//./\\.}" \
  | sort -u)

# Si no hay resultados parseables
if [ -z "$results" ]; then
  echo "[INFO] Sin subdominios parseables desde crt.sh para: $ACTIVO"
  exit 0
fi

# Imprimir resultados
echo "$results"
exit 0
