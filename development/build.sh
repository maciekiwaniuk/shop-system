cp ../.env.dist ../.env

docker compose down
docker compose up -d --build --force-recreate
docker exec -it shop-system-backend composer install --optimize-autoloader --no-interaction --no-progress

echo -n "Waiting for MySQL database to be ready... "
spinner="/-\\|"
while ! docker exec shop-system-mysql mysqladmin ping -h"127.0.0.1" --silent; do
    printf "\b${spinner:i++%4:1}"
    sleep 0.2
done

docker exec -it shop-system-backend php bin/console doctrine:migrations:migrate
docker exec -it shop-system-backend php bin/console lexik:jwt:generate-keypair
