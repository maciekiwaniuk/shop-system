#!/usr/bin/env bash
set -euo pipefail

if [ "$#" -ne 2 ]; then
  echo "Usage: $0 DOCKERHUB_USERNAME DOCKERHUB_PASSWORD"
  exit 1
fi

DOCKERHUB_USERNAME=$1
DOCKERHUB_PASSWORD=$2

PAYMENTS_IMAGE_NAME="shop-system-payments"
IMAGE_TAG="latest"

FULL_PAYMENTS_IMAGE="${DOCKERHUB_USERNAME}/${PAYMENTS_IMAGE_NAME}:${IMAGE_TAG}"

echo "Logging in to Docker Hub as ${DOCKERHUB_USERNAME}"
echo "${DOCKERHUB_PASSWORD}" | docker login --username "${DOCKERHUB_USERNAME}" --password-stdin

echo "Building payments image: ${FULL_PAYMENTS_IMAGE}"
(cd .. && docker build -t "${FULL_PAYMENTS_IMAGE}" -f microservices/payments/Dockerfile microservices/payments)

echo "Pushing payments image: ${FULL_PAYMENTS_IMAGE}"
docker push "${FULL_PAYMENTS_IMAGE}"

echo "Logout from Docker Hub"
docker logout

echo "Done."
