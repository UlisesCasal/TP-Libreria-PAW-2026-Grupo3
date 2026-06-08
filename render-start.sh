#!/bin/bash
# ============================================================
# render-start.sh — Punto de entrada para Render (Docker)
#
# 1. Aplica el schema y los datos semilla si las tablas no
#    existen (idempotente gracias al CREATE IF NOT EXISTS).
# 2. Arranca el servidor PHP integrado.
# ============================================================
# Intentar aplicar el schema al inicio (safety net).
# Si falla (DB no lista aún, env vars no disponibles), no importa:
# el auto-seed en bootstrap.php lo hará en el primer request.
php db/seed.php 2>&1 || echo "[render-start] seed no disponible aún, se hará en el primer request"

# Arrancar el servidor PHP en el puerto de Render
php -S 0.0.0.0:${PORT:-10000} -t public
