#!/bin/bash
set -e

# Configurar puerto dinámico de Render (Render usa la variable $PORT, por defecto 10000 o 80)
PORT=${PORT:-80}
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Permisos para storage y bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear enlace simbólico de storage si no existe
php artisan storage:link || true

# Ejecutar migraciones en TiDB Cloud automáticamente
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force || true

# Iniciar Apache en primer plano
echo "Iniciando servidor web Apache en puerto $PORT..."
exec apache2-foreground
