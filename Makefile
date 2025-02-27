.PHONY: build up down

build:
	docker compose build --no-cache

up:
	docker compose up --pull always -d --wait

down:
	docker compose down --remove-orphans