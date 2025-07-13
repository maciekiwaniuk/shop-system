./remove-containers-data.sh

cp ../.env.dist ../.env

docker ps -a -q --filter network=shop_system_network | xargs -r docker rm -f
docker compose up -d --build --force-recreate
docker exec -it shop-system-backend composer install --optimize-autoloader --no-interaction --no-progress
docker exec -it shop-system-backend php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

echo -n "Waiting for MySQL database container to be ready... "
spinner="/-\\|"
while ! docker exec shop-system-mysql mysqladmin ping -h"127.0.0.1" --silent; do
    printf "\b${spinner:i++%4:1}"
    sleep 0.2
done

docker exec -it shop-system-backend php bin/console doctrine:migrations:migrate --no-interaction

echo -n "Waiting for Elasticsearch container to be ready... "
while ! docker exec shop-system-elasticsearch curl -s -f "http://localhost:9200/_cluster/health?wait_for_status=yellow" > /dev/null; do
    printf "\b${spinner:i++%4:1}"
    sleep 0.2
done

docker exec -it shop-system-backend php bin/console commerce:elasticsearch:create-product-index --no-interaction
