#!/bin/bash
set -e

echo "Cleaning up cluster from shop-system namespace"

if kubectl get namespace shop-system >/dev/null 2>&1; then
    echo "Deleting..."

    kubectl delete all --all -n shop-system
    kubectl delete pvc --all -n shop-system
    kubectl delete configmap --all -n shop-system
    kubectl delete namespace shop-system

    echo "Successfully deleted resources"
else
    echo "Something went wrong..."
fi
