# Shop system

> ### Project of the backend of shop

## General info

- This is project of backend of basic shop system. 
- App is structured using DDD architecture, based on CQRS.
- There are used a few tools which help create valuable code: phpstan, deptrac, codesniffer.
- Application is well-tested. Tests are divided into Application (E2E), Integration and Unit tests.
- Validation of data is based on DTO-Constraints way.
- There is implemented caching system based on Redis.

## Used design patterns

- Proxy used for example in Caching, in CacheProxy

# Commands

Run application

    docker-compose up -d

Tests all in one using shell script

    docker-compose exec php bin/tests.sh

Tests all using command

    docker-compose exec php vendor/bin/phpcs && docker-compose exec php vendor/bin/deptrac analyse &&  docker-compose exec php vendor/bin/phpstan analyse && docker-compose exec php bin/phpunit