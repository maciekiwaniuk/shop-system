#!/bin/bash
set -e

echo "Deploying backend to kubernetes..."

echo "Creating namespace..."
kubectl apply -f namespace.yaml

echo "Deploying config maps..."
kubectl apply -f config-maps/

echo "Deploying secrets..."
kubectl apply -f secrets/

echo "Deploying services..."
kubectl apply -f services/

echo "Deploying applications..."
kubectl apply -f app/
