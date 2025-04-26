# scripts might not be working for now

# enable management plugin to access rabbitmq through web
docker compose exec shop-system-rabbitmq bash
cd sbin && rabbitmq-plugins enable rabbitmq_management
