response=$(curl -s "https://crt.sh/?q=%25.${DOMAIN}&output=json")

if echo "$response" | jq empty >/dev/null 2>&1; then
  echo "$response" | jq -r '.[].name_value' \
  | sed 's/\*\.//g' \
  | tr '\n' ',' \
  | tr ',' '\n' \
  | sort -u
else
  echo "[!] crt.sh no devolvió JSON válido para $DOMAIN"
fi
