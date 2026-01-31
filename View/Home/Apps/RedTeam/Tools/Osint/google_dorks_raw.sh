#!/bin/bash
set -euo pipefail

DOMAIN="${1:-}"
DATE="$(date -u '+%Y-%m-%d %H:%M:%S UTC')"

if [[ -z "$DOMAIN" ]]; then
  echo "[ERROR] Dominio no recibido"
  exit 2
fi

# Normalizar: si te pasan https://... o http://..., lo saco
DOMAIN="${DOMAIN#http://}"
DOMAIN="${DOMAIN#https://}"
DOMAIN="${DOMAIN%%/*}"

echo "# =============================================="
echo "# OSINT Google Dorks + Wayback"
echo "# Dominio: $DOMAIN"
echo "# Fecha: $DATE"
echo "# Fuente: Internet Archive (pasivo)"
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

# Timeouts (para que NO se cuelgue jamás)
CURL_CONNECT_TIMEOUT=5
CURL_MAX_TIME=20

for f in "${FILES[@]}"; do
  echo "[+] Buscando historial de: /$f"

  # CDX: devolvemos pocos campos, filtramos 200, colapsamos duplicados
  # Nota: esto sigue siendo pasivo (solo consulta Wayback)
  url="https://web.archive.org/cdx/search/cdx?url=${DOMAIN}/${f}&output=json&fl=timestamp,original,statuscode,mimetype&filter=statuscode:200&collapse=digest"

  # Si Wayback está lento, esto corta solo (no cuelga apache)
  resp="$(curl -sS -L \
    --connect-timeout "$CURL_CONNECT_TIMEOUT" \
    --max-time "$CURL_MAX_TIME" \
    "$url" || true)"

  # Si no hay respuesta, seguimos
  if [[ -z "$resp" ]]; then
    echo "  (sin respuesta / timeout)"
    echo
    continue
  fi

  # Parseo: saltar header [0], imprimir lindo "FOUND: <timestamp> <url> <mime>"
  # Si jq falla por respuesta rara, no rompe todo el script
  echo "$resp" | jq -r '
    if type=="array" and length>1 then
      .[1:][] | "  FOUND: \(.[] | tostring)" 
    else
      empty
    end
  ' 2>/dev/null || echo "  (respuesta no parseable)"

  echo
done

exit 0
