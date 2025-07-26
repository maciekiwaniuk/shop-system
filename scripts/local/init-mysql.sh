#!/bin/bash
set -e

echo "Starting initialization of mysql databases..."

echo "Check if pod with databse is already running..."
if ! kubectl get pods -n shop-system | grep -q "mysql.*Running"; then
    echo "MySQL pod is not running! Check mysql pod or whole cluster"
    exit 1
fi

echo "MYSQL pod is running"

echo "Waiting for mysql pod to be ready for script"
MYSQL_POD=$(kubectl get pods -n shop-system -l app.kubernetes.io/component=mysql -o jsonpath='{.items[0].metadata.name}')
while ! kubectl exec -it $MYSQL_POD -n shop-system -- mysqladmin ping -h"127.0.0.1" --silent; do
    sleep 1
done

kubectl exec -i $MYSQL_POD -n shop-system -- mysql -u root -proot_password<<SQL
    CREATE DATABASE IF NOT EXISTS \`shop_system_auth\`;
    CREATE DATABASE IF NOT EXISTS \`shop_system_auth_test\`;
    CREATE DATABASE IF NOT EXISTS \`shop_system_commerce\`;
    CREATE DATABASE IF NOT EXISTS \`shop_system_commerce_test\`;
    GRANT ALL PRIVILEGES ON *.* TO 'shop_user'@'%';
    FLUSH PRIVILEGES;
SQL

echo "Script completed, databases in pod:"

kubectl exec $MYSQL_POD -n shop-system -- mysql -u root -proot_password -e "SHOW DATABASES;"
