CREATE DATABASE IF NOT EXISTS `shop_system_auth`;
CREATE DATABASE IF NOT EXISTS `shop_system_auth_test`;
CREATE DATABASE IF NOT EXISTS `shop_system_commerce`;
CREATE DATABASE IF NOT EXISTS `shop_system_commerce_test`;
CREATE DATABASE IF NOT EXISTS `shop_system_payments`;
CREATE DATABASE IF NOT EXISTS `shop_system_payments_test`;

GRANT ALL PRIVILEGES ON *.* TO 'shop_user'@'%';
