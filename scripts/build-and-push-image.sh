#!/usr/bin/env bash
set -euo pipefail

if [ "$#" -ne 2 ]; then
  echo "Usage: $0 DOCKERHUB_USERNAME DOCKERHUB_PASSWORD"
  exit 1
fi

DOCKERHUB_USERNAME=$1
DOCKERHUB_PASSWORD=$2

BACKEND_IMAGE_NAME="shop-system-backend"
IMAGE_TAG="latest"

FULL_BACKEND_IMAGE="${DOCKERHUB_USERNAME}/${BACKEND_IMAGE_NAME}:${IMAGE_TAG}"

echo "Logging in to Docker Hub as ${DOCKERHUB_USERNAME}"
echo "${DOCKERHUB_PASSWORD}" | docker login --username "${DOCKERHUB_USERNAME}" --password-stdin

echo "Building backend image: ${FULL_BACKEND_IMAGE}"
docker build -t "${FULL_BACKEND_IMAGE}" -f ./php.Dockerfile .

echo "Pushing backend image: ${FULL_BACKEND_IMAGE}"
docker push "${FULL_BACKEND_IMAGE}"

echo "Logout from Docker Hub"
docker logout

echo "Done."
