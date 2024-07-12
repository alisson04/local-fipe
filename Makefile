PHP_SERVICE=www

#Docker Compose Commands
up:
	docker compose up

restart:
	docker compose restart

stop:
	docker compose stop

down:
	docker compose down

#Composer Commands
composer-install:
	docker compose exec $(PHP_SERVICE) sh -c "composer install"

composer-update:
	docker compose exec $(PHP_SERVICE) composer update

composer-require:
	docker compose exec $(PHP_SERVICE) composer require $(package)