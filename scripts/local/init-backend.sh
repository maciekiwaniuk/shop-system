#!/bin/bash
set -e

echo "Initialization of backend"

BACKEND_POD=$(kubectl get pods -n shop-system -l app=shop-backend -o jsonpath='{.items[0].metadata.name}')
kubectl wait --for=condition=ready pod/$BACKEND_POD -n shop-system --timeout=300s

kubectl exec -it $BACKEND_POD -n shop-system -- composer install --optimize-autoloader --no-interaction --no-progress
kubectl exec -it $BACKEND_POD -n shop-system -- php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

echo "Waiting mysql pod to be ready for migrations"
MYSQL_POD=$(kubectl get pods -n shop-system -l app=mysql -o jsonpath='{.items[0].metadata.name}')
while ! kubectl exec -it $MYSQL_POD -n shop-system -- mysqladmin ping -h"127.0.0.1" --silent; do
    sleep 1
done

kubectl exec -it $BACKEND_POD -n shop-system -- php bin/console doctrine:migrations:migrate --no-interaction

echo "Waiting elasticsearch pod to be ready for creation of indices"
ELASTICSEARCH_POD=$(kubectl get pods -n shop-system -l app=elasticsearch -o jsonpath='{.items[0].metadata.name}')
while ! kubectl exec -it $ELASTICSEARCH_POD -n shop-system -- curl -s -f "http://localhost:9200/_cluster/health?wait_for_status=yellow" > /dev/null; do
    sleep 1
done

kubectl exec -it $BACKEND_POD -n shop-system -- php bin/console commerce:elasticsearch:create-product-index --env=dev --no-interaction
kubectl exec -it $BACKEND_POD -n shop-system -- php bin/console commerce:elasticsearch:create-product-index --env=test --no-interaction
