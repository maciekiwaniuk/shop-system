#!/bin/bash

echo '######## Running deptrac tests ########'
php vendor/bin/deptrac analyse

echo '######## Running phpstan tests ########'
php vendor/bin/phpstan analyse

echo '######## Running phpunit tests ########'
php bin/phpunit
