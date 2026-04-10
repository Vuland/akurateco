.PHONY: init start stop restart ssh composer

SERVICE ?= app

init:
	docker compose build
	docker compose run --rm $(SERVICE) composer install --no-interaction --no-progress

start:
	docker compose up -d

stop:
	docker compose down

restart: stop start

ssh:
	docker compose exec $(SERVICE) sh

composer:
	docker compose run --rm $(SERVICE) composer $(ARGS)

