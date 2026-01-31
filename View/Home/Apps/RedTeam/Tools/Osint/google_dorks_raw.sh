#!/usr/bin/env bash

# ===============================
# Google Dorks OSINT Generator
# ===============================

if [ -z "$1" ]; then
  echo "[ERROR] Dominio no especificado"
  exit 1
fi

DOMAIN="$1"
DATE=$(date -u +"%Y-%m-%d %H:%M:%S UTC")

echo "# =============================================="
echo "# Google Dorks OSINT - Dominio: $DOMAIN"
echo "# Generado: $DATE"
echo "# =============================================="
echo

# --- Enumeración básica
echo "site:$DOMAIN"
echo "site:*.$DOMAIN"
echo

# --- Archivos sensibles
echo "site:$DOMAIN ext:env"
echo "site:$DOMAIN ext:log"
echo "site:$DOMAIN ext:sql"
echo "site:$DOMAIN ext:bak"
echo "site:$DOMAIN ext:old"
echo "site:$DOMAIN ext:backup"
echo

# --- Configuración
echo "site:$DOMAIN ext:conf"
echo "site:$DOMAIN ext:cfg"
echo "site:$DOMAIN ext:ini"
echo "site:$DOMAIN ext:yml"
echo "site:$DOMAIN ext:yaml"
echo

# --- Repositorios
echo "site:$DOMAIN ext:git"
echo "site:$DOMAIN \".git\""
echo "site:$DOMAIN ext:svn"
echo

# --- Auth / Panels
echo "site:$DOMAIN inurl:login"
echo "site:$DOMAIN inurl:admin"
echo "site:$DOMAIN inurl:auth"
echo "site:$DOMAIN inurl:dashboard"
echo

# --- Errores expuestos
echo "site:$DOMAIN \"stack trace\""
echo "site:$DOMAIN \"undefined index\""
echo "site:$DOMAIN \"fatal error\""
echo

# --- Directory listing
echo "site:$DOMAIN \"index of\""
echo "site:$DOMAIN \"parent directory\""
echo

# --- Credenciales / secretos
echo "site:$DOMAIN \"AKIA\""
echo "site:$DOMAIN \"BEGIN RSA PRIVATE KEY\""
echo "site:$DOMAIN \"api_key\""
echo "site:$DOMAIN \"secret\""
