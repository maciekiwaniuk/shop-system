#!/bin/bash
set -e

echo "Deploying system to kubernetes..."

./run.sh

MINIKUBE_IP=$(minikube ip)
echo "Minikube IP: $MINIKUBE_IP"

cd ../../k8s/local

echo "Creating namespace..."
kubectl apply -f namespace.yaml

echo "Deploying storage..."
kubectl apply -f storage/

echo "Deploying config maps..."
kubectl apply -f config-maps/

echo "Deploying secrets..."
kubectl apply -f secrets/

echo "Deploying services..."
kubectl apply -f services/

echo "Deploying applications..."
kubectl apply -f app/

cd ../../scripts/local

echo "Wait 10 seconds for all pods to initialize"
sleep 10

echo "Running scripts..."
./init-mysql.sh
./init-backend.sh
./display-access.sh
