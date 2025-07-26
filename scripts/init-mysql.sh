#!/bin/bash
set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🗄️  MySQL Database Initialization${NC}"
echo "=================================="

# Check if MySQL pod is running
echo -e "${BLUE}📦 Checking MySQL pod status...${NC}"
if ! kubectl get pods -n shop-system | grep -q "mysql.*Running"; then
    echo -e "${YELLOW}⚠️  MySQL pod is not running. Please start your Kubernetes deployment first.${NC}"
    echo "Run: ./scripts/deploy-backend.sh"
    exit 1
fi

echo -e "${GREEN}✅ MySQL pod is running${NC}"

# Get MySQL pod name
MYSQL_POD=$(kubectl get pods -n shop-system -l app=mysql -o jsonpath='{.items[0].metadata.name}')
echo -e "${BLUE}📍 MySQL pod: $MYSQL_POD${NC}"

# Wait for MySQL to be ready
echo -e "${BLUE}⏳ Waiting for MySQL to be ready...${NC}"
kubectl wait --for=condition=ready pod/$MYSQL_POD -n shop-system --timeout=300s

# Execute the initialization script
echo -e "${BLUE}🚀 Running database initialization...${NC}"
kubectl exec -i $MYSQL_POD -n shop-system -- mysql -u root -proot_password << 'EOF'
CREATE DATABASE IF NOT EXISTS `shop_system_auth`;
CREATE DATABASE IF NOT EXISTS `shop_system_auth_test`;
CREATE DATABASE IF NOT EXISTS `shop_system_commerce`;
CREATE DATABASE IF NOT EXISTS `shop_system_commerce_test`;

GRANT ALL PRIVILEGES ON *.* TO 'shop_user'@'%';
FLUSH PRIVILEGES;
EOF

echo -e "${GREEN}✅ Database initialization completed!${NC}"

# Verify the databases were created
echo -e "${BLUE}🔍 Verifying databases...${NC}"
kubectl exec $MYSQL_POD -n shop-system -- mysql -u root -proot_password -e "SHOW DATABASES;"

echo -e "${GREEN}✅ All databases created successfully!${NC}" 