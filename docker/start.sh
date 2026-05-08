#!/bin/bash

echo "Starting Laravel application in Docker..."

# Ожидание базы данных
echo "Waiting for database..."
while ! nc -z database 3306; do
  sleep 1
done
echo "Database is ready!"

# Проверка установлен ли Laravel
if [ ! -f "artisan" ]; then
    echo "Laravel not found in container. Please mount your Laravel project."
    exit 1
fi

# Используем .env из Docker папки (уже должен быть скопирован)
if [ -f "../.env" ]; then
    echo "Using Docker .env file..."
    # Убедимся что используем правильные настройки для Docker
    php artisan config:clear
    php artisan cache:clear
fi

# Установка зависимостей
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

# Генерация ключа если не установлен
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Настройка прав
echo "Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Миграции базы данных
echo "Running database migrations..."
php artisan migrate --force

# Оптимизация
echo "Optimizing application..."
php artisan optimize
php artisan config:cache
php artisan route:cache

echo "Laravel is ready in Docker!"
php-fpm
