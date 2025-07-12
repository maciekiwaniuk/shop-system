#!/bin/bash

echo '######## Running codeSniffer tests ########'
php vendor/bin/phpcs

echo '######## Running deptrac tests ########'
php vendor/bin/deptrac analyse

echo '######## Running phpstan tests ########'
php vendor/bin/phpstan analyse

echo '######## Running phpunit tests ########'
php vendor/bin/phpunit
