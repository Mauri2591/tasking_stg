#!/bin/bash

ACTIVO="$1"

if [ -z "$ACTIVO" ]; then
  echo "[ERROR] Dominio no recibido"
  exit 2
fi

curl -s \
  --http1.1 \
  --max-time 40 \
  -A "Mozilla/5.0" \
  "https://crt.sh/?q=%25.${ACTIVO}&output=json"
