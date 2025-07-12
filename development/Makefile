drop_migrations:
	docker compose exec shop-system php bin/console doctrine:schema:drop --full-database --force

load_fixtures:
	docker compose exec shop-system php bin/console doctrine:fixtures:load

test:
	docker compose exec shop-system bin/tests.sh

fix_codesniffer:
	docker compose exec shop-system php vendor/bin/phpcbf

cache_clear:
	docker compose exec shop-system php bin/console cache:clear

diff:
	bin/console doctrine:migrations:diff --em=commerce --namespace=DoctrineMigrationsCommerce

schema_update:
	bin/console doctrine:schema:update --force --em=database2

clear_database:
	sudo chmod -R 777 docker/mysql/data
	sudo rm -R docker/mysql/data
	docker compose up -d shop-system-mysql --build --force-recreate

simple_migration_of_all_databases:
	docker compose exec shop-system php bin/console doctrine:migrations:migrate

consume_queue:
	docker compose exec shop-system php bin/console messenger:consume async -vv

delete_var:
	sudo chmod -R 777 var
	rm -r var

remove_containers:
	docker stop $(docker ps -q) && docker rm $(docker ps -a -q)