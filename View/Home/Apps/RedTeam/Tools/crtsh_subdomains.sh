#!/bin/bash

DOMAIN="$1"

if [ -z "$DOMAIN" ]; then
  echo "ERROR: Dominio no recibido"
  exit 1
fi

curl -s "https://crt.sh/?q=%25.${DOMAIN}&output=json" \
| jq -r '.[].name_value' \
| sed 's/\*\.//g' \
| tr '\n' ',' \
| tr ',' '\n' \
| sort -u
