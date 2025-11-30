#!/bin/bash
set -e

echo "Deploying payments..."

echo "Creating namespace..."
kubectl apply -f namespace.yaml

echo "Deploying config map..."
kubectl apply -f payments/config-map.yaml

echo "Deploying secret..."
kubectl apply -f payments/secret.yaml

echo "Deploying service..."
kubectl apply -f payments/service.yaml

echo "Deploying deployment..."
kubectl apply -f payments/deployment.yaml
