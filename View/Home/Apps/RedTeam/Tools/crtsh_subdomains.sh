#!/bin/bash

if [ "$#" -lt 1 ]; then
  echo "[ERROR] No se recibieron dominios"
  exit 2
fi

for ACTIVO in "$@"; do
  if [[ "$ACTIVO" != *.* ]]; then
    echo "[WARN] Activo sin TLD válido"
    continue
  fi

  URL="https://crt.sh/?q=%25.${ACTIVO}&output=json"
  response=""

  for i in {1..3}; do
    response=$(curl -s \
      --http1.1 \
      --max-time 40 \
      -A "Mozilla/5.0" \
      "$URL")

    if [ -n "$response" ]; then
      break
    fi

    sleep 3
  done

  if [ -z "$response" ]; then
    echo "[WARN] crt.sh no devolvió contenido tras reintentos"
    continue
  fi

  results=$(echo "$response" \
    | sed 's/\\n/\n/g' \
    | grep -oE "([a-zA-Z0-9_-]+\.)+${ACTIVO//./\\.}" \
    | sort -u)

  if [ -z "$results" ]; then
    echo "[INFO] Sin subdominios parseables"
    continue
  fi

  echo "$results"
done
