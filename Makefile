PROJECT_NAME = symfony
PHP_CONTAINER = symfony_php
NGINX_CONTAINER = symfony_nginx
DB_CONTAINER = symfony_postgres

up:
	docker compose up -d

down:
	docker compose down

stop:
	docker compose stop

restart:
	docker compose down && docker compose up -d

build:
	docker compose build

logs:
	docker compose logs -f

bash:
	docker exec -it $(PHP_CONTAINER) bash

composer:
	docker exec -it $(PHP_CONTAINER) composer

migrate:
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction

cache-clear:
	docker exec -it $(PHP_CONTAINER) php bin/console cache:clear

consumer:
	docker exec -it $(PHP_CONTAINER) php bin/console messenger:consume async

permissions:
	sudo chown -R $$(id -u):$$(id -g) symfony-app/var

init:
	docker compose down -v --remove-orphans
	docker compose build
	docker compose up -d
	sleep 10
	# Eliminar la base de datos si existe
	docker exec -i $(DB_CONTAINER) psql -U postgres -c "DROP DATABASE IF EXISTS symfony_db;"
	# Crear la base de datos
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:database:create --no-interaction
	# Ejecutar migraciones
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction


.PHONY: up down restart build logs bash composer migrate fixtures cache-clear permissions init
