copy_env:
	cp .env.dist .env

initialize:
	docker compose exec php php bin/console doctrine:migrations:diff
	docker compose exec php php bin/console doctrine:migrations:migrate
	docker compose exec php php bin/console lexik:jwt:generate-keypair

run:
	docker compose --profile dev up -d

run_prod:
	docker compose --profile prod up -d

drop_migrations:
	docker compose exec php bin/console doctrine:schema:drop --full-database --force

migrate:
	docker compose exec php bin/console doctrine:migrations:diff
	docker compose exec php bin/console doctrine:migrations:migrate

load_fixtures:
	docker compose exec php bin/console doctrine:fixtures:load

test:
	docker compose exec php bin/tests.sh

fix_codesniffer:
	docker compose exec php vendor/bin/phpcbf

cache_clear:
	docker compose exec php bin/console cache:clear

install:
	docker compose exec php composer install --optimize-autoloader --no-interaction --no-progress

diff:
	bin/console doctrine:migrations:diff --em=commerce --namespace=DoctrineMigrationsCommerce

schema_update:
	bin/console doctrine:schema:update --force --em=database2
