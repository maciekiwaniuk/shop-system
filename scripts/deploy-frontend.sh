#!/bin/bash
set -e

echo "Deploying frontend..."

echo "Creating namespace..."
kubectl apply -f namespace.yaml

echo "Deploying config map..."
kubectl apply -f frontend/config-map.yaml

echo "Deploying service..."
kubectl apply -f frontend/service.yaml

echo "Deploying deployment..."
kubectl apply -f frontend/deployment.yaml
