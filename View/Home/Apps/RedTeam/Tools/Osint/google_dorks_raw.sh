#!/bin/bash
# ===================================================
# OSINT PASIVO - Google Dorks (GENERADOR)
# No ejecuta búsquedas, solo genera consultas
# ===================================================

ACTIVO="$1"

if [ -z "$ACTIVO" ]; then
  echo "[ERROR] Dominio no recibido"
  exit 1
fi

# Validación básica de TLD
if ! [[ "$ACTIVO" =~ \. ]]; then
  echo "[WARN] Dominio sin TLD válido: $ACTIVO"
  exit 0
fi

echo "===== Google Dorks para: $ACTIVO ====="
echo

# Archivos sensibles
echo "site:$ACTIVO filetype:env"
echo "site:$ACTIVO filetype:log"
echo "site:$ACTIVO filetype:sql"
echo "site:$ACTIVO filetype:bak"
echo "site:$ACTIVO filetype:old"
echo "site:$ACTIVO filetype:zip"
echo "site:$ACTIVO filetype:tar"
echo "site:$ACTIVO filetype:gz"
echo

# Configuraciones y credenciales
echo "site:$ACTIVO \"password=\""
echo "site:$ACTIVO \"apikey\""
echo "site:$ACTIVO \"secret\""
echo "site:$ACTIVO \"token\""
echo

# Paneles y accesos
echo "site:$ACTIVO inurl:login"
echo "site:$ACTIVO inurl:admin"
echo "site:$ACTIVO inurl:dashboard"
echo "site:$ACTIVO intitle:\"index of\""
echo

# Errores y debug
echo "site:$ACTIVO \"Warning:\""
echo "site:$ACTIVO \"Fatal error\""
echo "site:$ACTIVO \"Undefined index\""
echo

exit 0
