#!/bin/bash
set -e

# Configurar puerto dinámico de Render (Render usa la variable $PORT, por defecto 10000 o 80)
PORT=${PORT:-80}
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Asegurar directorios de storage y permisos
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
touch /var/www/html/storage/logs/laravel.log

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear enlace simbólico de storage si no existe
php artisan storage:link || true

# Limpiar y recrear caché de configuración
php artisan config:clear || true

# Ejecutar migraciones y seeders en TiDB Cloud
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force || true
php artisan db:seed --force || true

# Reasignar permisos finales para que Apache (www-data) tenga control total de logs y sesiones
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Iniciar Apache en primer plano
echo "Iniciando servidor web Apache en puerto $PORT..."
exec apache2-foreground
