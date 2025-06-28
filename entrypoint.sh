#!/bin/bash

# Attendre que la base de données soit prête
echo "create database"
php bin/console doctrine:database:create --if-not-exists --no-interaction

# Exécuter les migrations
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Ajout des utilisateurs par défaut
echo "create default users"
php bin/console app:add:users

# Lancer PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
