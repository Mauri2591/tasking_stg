#!/bin/bash

DOMAIN="$1"

if [ -z "$DOMAIN" ]; then
    echo "No domain provided"
    exit 1
fi

# Query crt.sh (JSON)
curl -s "https://crt.sh/?q=%25.${DOMAIN}&output=json" \
| jq -r '.[].name_value' 2>/dev/null \
| sed 's/\*\.//' \
| tr '\n' '\n' \
| sort -u

exit 0
