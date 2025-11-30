#!/bin/bash
set -e

echo "Deploying tools..."

cd ../../k8s/local

kubectl apply -f namespace.yaml

kubectl apply -f tools/elasticsearch
kubectl apply -f tools/mailhog
kubectl apply -f tools/mysql
kubectl apply -f tools/rabbitmq
kubectl apply -f tools/redis
