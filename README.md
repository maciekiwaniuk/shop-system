# Shop system

> ### Project of the backend of shop

# Docker configuration

Run application

    docker-compose up -d

Tests all in one using shell script

    docker-compose exec php bin/tests.sh

Tests all using command

    docker-compose exec php vendor/bin/phpcs && docker-compose exec php vendor/bin/deptrac analyse &&  docker-compose exec php vendor/bin/phpstan analyse && docker-compose exec php bin/phpunit