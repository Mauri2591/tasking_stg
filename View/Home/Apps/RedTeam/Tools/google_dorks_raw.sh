    #!/bin/bash
# ------------------------------------------------------------
# OSINT PASIVO - Google Dorks (generación)
# No ejecuta búsquedas, solo genera dorks
# ------------------------------------------------------------

DOMINIO="$1"

if [ -z "$DOMINIO" ]; then
  echo "[ERROR] Dominio no recibido"
  exit 2
fi

# Validación básica FQDN
if [[ "$DOMINIO" != *.* ]]; then
  echo "[WARN] Dominio sin TLD válido: $DOMINIO"
  exit 0
fi

echo "# =============================================="
echo "# Google Dorks OSINT - Dominio: $DOMINIO"
echo "# Generado: $(date -u)"
echo "# =============================================="
echo

# --- Dorks básicos ---
echo "site:$DOMINIO"
echo "site:*.$DOMINIO"
echo

# --- Archivos sensibles ---
echo "site:$DOMINIO ext:env"
echo "site:$DOMINIO ext:log"
echo "site:$DOMINIO ext:sql"
echo "site:$DOMINIO ext:bak"
echo "site:$DOMINIO ext:old"
echo "site:$DOMINIO ext:backup"
echo

# --- Configuraciones ---
echo "site:$DOMINIO ext:conf"
echo "site:$DOMINIO ext:cfg"
echo "site:$DOMINIO ext:ini"
echo "site:$DOMINIO ext:yml"
echo "site:$DOMINIO ext:yaml"
echo

# --- Código / repositorios expuestos ---
echo "site:$DOMINIO ext:git"
echo "site:$DOMINIO \".git\""
echo "site:$DOMINIO ext:svn"
echo

# --- Autenticación / paneles ---
echo "site:$DOMINIO inurl:login"
echo "site:$DOMINIO inurl:admin"
echo "site:$DOMINIO inurl:auth"
echo "site:$DOMINIO inurl:dashboard"
echo

# --- Errores / debug ---
echo "site:$DOMINIO \"stack trace\""
echo "site:$DOMINIO \"undefined index\""
echo "site:$DOMINIO \"fatal error\""
echo

# --- Backup web ---
echo "site:$DOMINIO index of"
echo "site:$DOMINIO \"parent directory\""
echo

# --- Cloud / tokens ---
echo "site:$DOMINIO \"AKIA\""
echo "site:$DOMINIO \"BEGIN RSA PRIVATE KEY\""
echo "site:$DOMINIO \"api_key\""
echo "site:$DOMINIO \"secret\""

exit 0
