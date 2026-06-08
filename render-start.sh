#!/bin/bash
# ============================================================
# render-start.sh — Punto de entrada para Render (Docker)
#
# 1. Aplica el schema y los datos semilla si las tablas no
#    existen (idempotente gracias al CREATE IF NOT EXISTS).
# 2. Arranca el servidor PHP integrado.
# ============================================================
set -e

# Ejecutar el seed (solo crea tablas si no existen)
php db/seed.php

# Arrancar el servidor PHP en el puerto de Render
php -S 0.0.0.0:${PORT:-10000} -t public
