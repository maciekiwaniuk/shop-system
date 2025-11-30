#!/bin/bash
set -e

if ! minikube status | grep -q "Running"; then
    echo "Minikube is not working. Starting minikube..."
    minikube start --driver=docker
    minikube addons enable storage-provisioner
else
    echo "Minikube is already working"
fi
