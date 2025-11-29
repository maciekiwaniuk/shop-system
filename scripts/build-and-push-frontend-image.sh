#!/usr/bin/env bash
set -euo pipefail

if [ "$#" -ne 2 ]; then
  echo "Usage: $0 DOCKERHUB_USERNAME DOCKERHUB_PASSWORD"
  exit 1
fi

DOCKERHUB_USERNAME=$1
DOCKERHUB_PASSWORD=$2

FRONTEND_IMAGE_NAME="shop-system-frontend"
IMAGE_TAG="latest"

FULL_FRONTEND_IMAGE="${DOCKERHUB_USERNAME}/${FRONTEND_IMAGE_NAME}:${IMAGE_TAG}"

echo "Logging in to Docker Hub as ${DOCKERHUB_USERNAME}"
echo "${DOCKERHUB_PASSWORD}" | docker login --username "${DOCKERHUB_USERNAME}" --password-stdin

echo "Building frontend image: ${FULL_FRONTEND_IMAGE}"
(cd .. && docker build -t "${FULL_FRONTEND_IMAGE}" -f frontend/Dockerfile frontend)

echo "Pushing frontend image: ${FULL_FRONTEND_IMAGE}"
docker push "${FULL_FRONTEND_IMAGE}"

echo "Logout from Docker Hub"
docker logout

echo "Done."
