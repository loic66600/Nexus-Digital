#!/bin/bash

# Configure Composer to allow contrib recipes
docker exec -it phpimmo composer config extra.symfony.allow-contrib true

# Install Composer dependencies
docker exec -it phpimmo composer install

# Run Yarn
docker exec -it nodeimmo yarn

# Run Yarn encore dev
docker exec -it nodeimmo yarn encore dev

# Run Yarn encore dev --watch
docker exec -it nodeimmo yarn encore dev --watch &

# Run Yarn add aos
docker exec -it nodeimmo yarn add aos

# Require symfony/notifier
yes | docker exec -it phpimmo composer require symfony/notifier

# Require fakerphp/faker --dev
yes | docker exec -it phpimmo composer require fakerphp/faker --dev

# Require stripe/stripe-php
yes | docker exec -it phpimmo composer require stripe/stripe-php

# Run doctrine migrations to construct the database
docker exec -it phpimmo bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures to quickly populate the database with data
docker exec -it phpimmo bin/console doctrine:fixtures:load --no-interaction

echo "All commands executed successfully!"





