#!/bin/bash
set -e

echo "Deploying all pods..."

cd ../../k8s/local
../../scripts/deploy-backend.sh
../../scripts/deploy-payments.sh
../../scripts/deploy-frontend.sh
