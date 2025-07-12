sudo find docker/elasticsearch/data -mindepth 1 -not -name '.gitignore' -exec rm -rf {} +
sudo find docker/mysql/data -mindepth 1 -not -name '.gitignore' -exec rm -rf {} +
sudo find docker/rabbitmq/data -mindepth 1 -not -name '.gitignore' -exec rm -rf {} +
sudo find docker/redis/data -mindepth 1 -not -name '.gitignore' -exec rm -rf {} +
