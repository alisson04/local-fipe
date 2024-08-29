PHP_SERVICE=www

#RESPONSES COMMANDS
#========================================
responses-update:
	docker compose exec $(PHP_SERVICE) php -d memory_limit=1000M -f updateFileResponses.php

responses-count-all:
	echo "This can take a few minutes or more..."
	docker compose exec $(PHP_SERVICE) bash -c "find /var/www/responses -type f | wc -l"

responses-count-brands:
	echo "This can take a few minutes or more..."
	docker compose exec $(PHP_SERVICE) bash -c "find /var/www/responses/consultarmarcas -type f | wc -l"

responses-count-year-model:
	echo "This can take a few minutes or more..."
	docker compose exec $(PHP_SERVICE) bash -c "find /var/www/responses/consultaranomodelo -type f | wc -l"

responses-show-in-project:
	echo "Be sure of what you doing... this will take a few minutes or more and will slow down your GIT and Docker..."
	docker compose exec $(PHP_SERVICE) cp -rn /var/www/responses /var/www/html/responses

#DOCKER COMPOSE COMMANDS
#========================================
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

#COMPOSER COMMANDS
#========================================
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