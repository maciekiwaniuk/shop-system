copy_env:
	cp .env.dist .env

initialize:
	docker compose exec shop-system php bin/console doctrine:migrations:diff
	docker compose exec shop-system php bin/console doctrine:migrations:migrate
	docker compose exec shop-system php bin/console lexik:jwt:generate-keypair

drop_migrations:
	docker compose exec shop-system php bin/console doctrine:schema:drop --full-database --force

migrate:
	docker compose exec shop-system php bin/console doctrine:migrations:diff
	docker compose exec shop-system php bin/console doctrine:migrations:migrate

load_fixtures:
	docker compose exec shop-system php bin/console doctrine:fixtures:load

test:
	docker compose exec shop-system bin/tests.sh

fix_codesniffer:
	docker compose exec shop-system php vendor/bin/phpcbf

cache_clear:
	docker compose exec shop-system php bin/console cache:clear

install:
	docker compose exec shop-system composer install --optimize-autoloader --no-interaction --no-progress

diff:
	bin/console doctrine:migrations:diff --em=commerce --namespace=DoctrineMigrationsCommerce

schema_update:
	bin/console doctrine:schema:update --force --em=database2
