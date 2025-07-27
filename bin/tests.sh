#!/bin/bash
set -e

echo '######## Running codeSniffer tests ########'
php vendor/bin/phpcs

echo '######## Running deptrac tests ########'
php vendor/bin/deptrac analyse

echo '######## Running phpstan tests ########'
php vendor/bin/phpstan analyse

echo '######## Running phpunit unit tests ########'
php vendor/bin/phpunit --group unit

echo '######## Running phpunit integration tests ########'
php vendor/bin/phpunit --group integration

echo '######## Running phpunit application tests ########'
php vendor/bin/phpunit --group application
