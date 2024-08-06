PHP_SERVICE=www

#Run Commands
update-file-responses:
	docker compose exec $(PHP_SERVICE) php -d memory_limit=500M -f updateFileResponses.php

#Docker Compose Commands
up:
	docker compose up -d

restart:
	docker compose restart

stop:
	docker compose stop

down:
	docker compose down

build:
	docker compose build $(PHP_SERVICE)
	make up

bash:
	docker compose exec $(PHP_SERVICE) bash

#Composer Commands
composer-install:
	docker compose exec $(PHP_SERVICE) sh -c "composer install"

composer-update:
	docker compose exec $(PHP_SERVICE) composer update

composer-dump:
	docker compose exec $(PHP_SERVICE) sh -c "composer dumpautoload"

composer-require:
	docker compose exec $(PHP_SERVICE) composer require $(package)

composer-show:
	docker compose exec $(PHP_SERVICE) composer show $(package)