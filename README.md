# Shop system

> ### Project of the backend of shop

![image](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![image](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=Symfony&logoColor=white)
![image](https://img.shields.io/badge/redis-%23DD0031.svg?&style=for-the-badge&logo=redis&logoColor=white)
![image](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![image](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)

## General info

- App is structured using DDD approach, based on the Command Query Responsibility Segregation (CQRS) pattern.
- Several tools are utilized to facilitate the creation of robust code: phpstan (6 level), deptrac, codesniffer.
- The application is well-tested, with tests categorized into Application (E2E), Integration, and Unit tests.
- A caching system based on Redis has been implemented.

## Technical solutions

- Directories are organized into /Module, which contains subdirectories for all modules, and /Shared, which includes 
components shared across all modules. Each module [Order, Product, User] comprises four directories:
    - Application -> houses the implementation of application logic
    - Domain -> contains things related strictly to business information - entities, enums and interfaces of repositories
    - Infrastructure -> includes components related to the technical layer, such as the database and caching system
    - UI -> contains console commands or controllers that serve as the connection between the user and the system
- Actions are classified into commands and queries. I have implemented a custom solution for storing information after 
the completion of actions. Each command returns a CommandResult, while queries return a QueryResult, which contains 
information about success, status codes, and retrieved data.
- Authentication is implemented using the JWT approach with the LexikJWTAuthenticationBundle.
- Event sourcing is used to store information about the current status of an order. The latest created order status 
indicates its current status.

### Database
![](https://github.com/maciekiwaniuk/shop-system/raw/main/public/images/database.jpg)

### API Docs

![](https://github.com/maciekiwaniuk/shop-system/raw/main/public/images/docs.jpg)

### Structure
![](https://github.com/maciekiwaniuk/shop-system/raw/main/public/images/modules.jpg)

### Module
![](https://github.com/maciekiwaniuk/shop-system/raw/main/public/images/module.jpg)

## Commands using Make

Initialize for first time run

    make initialize

Run development profile

    make run

Drop migrations, migrate and load fixtures

    make drop_migrations
    make migrate
    make load_fixtures

Run all tests

    make test

## Commands without Make

Initialize for first time run

    docker-compose up --profile dev -d
	docker-compose exec php bin/console doctrine:migrations:diff
	docker-compose exec php bin/console doctrine:migrations:migrate
	docker-compose exec php bin/console lexik:jwt:generate-keypair

#### For other commands check content of Makefile

## API Documentation

The API documentation is available at

    localhost:80/api/doc