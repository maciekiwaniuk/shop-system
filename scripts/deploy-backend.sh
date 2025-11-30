#!/bin/bash
set -e

echo "Deploying backend..."

echo "Creating namespace..."
kubectl apply -f namespace.yaml

echo "Deploying config map..."
kubectl apply -f backend/config-map.yaml

echo "Deploying secret..."
kubectl apply -f backend/secret.yaml

echo "Deploying service..."
kubectl apply -f backend/service.yaml

echo "Deploying deployment..."
kubectl apply -f backend/deployment.yaml

echo "Deploying queues..."
kubectl apply -f backend/queue.yaml
