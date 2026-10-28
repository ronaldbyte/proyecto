#!/bin/bash
set -e

# Configurar puerto dinámico de Render (Render usa la variable $PORT, por defecto 10000 o 80)
PORT=${PORT:-80}
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Determinar ruta de base de datos SQLite (Disco persistente /var/data si existe, o local)
if [ -d "/var/data" ]; then
    echo "Disco persistente detectado en /var/data"
    export DB_DATABASE=/var/data/database.sqlite
    touch /var/data/database.sqlite
    chown -R www-data:www-data /var/data
    chmod -R 777 /var/data
else
    export DB_DATABASE=/var/www/html/database/database.sqlite
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
    chmod -R 777 /var/www/html/database
fi

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

# Ejecutar migraciones y seeders solo si la base de datos es nueva/está vacía
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force || true
php artisan tinker --execute="if (\App\Models\Usuario::count() === 0) { \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]); }" || true

# Reasignar permisos finales para que Apache (www-data) tenga control total de escritura en SQLite y storage
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
if [ -d "/var/data" ]; then
    chown -R www-data:www-data /var/data
    chmod -R 777 /var/data
fi

# Iniciar Apache en primer plano
echo "Iniciando servidor web Apache en puerto $PORT..."
exec apache2-foreground
