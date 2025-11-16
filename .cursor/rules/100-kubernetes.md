# 10. Kubernetes Deployment (`k8s`)

This document provides an overview of the Kubernetes setup for both local development and production. The configurations are located in the `k8s/` directory.

**Note:** The local setup is more up-to-date but is still considered a work in progress and may not be fully functional. The production setup uses an older declaration style. For local development, the cluster is run using Minikube.

## Directory Structure

The `k8s/` directory is split into two main environments: `local/` and `prod/`.

### `k8s/local/`

This directory contains the Kubernetes manifests for running the entire system locally using a tool like Minikube. The goal is to replicate the production environment as closely as possible.

- **`backend/`**: Contains the deployment, service, and other necessary manifests for the PHP (Symfony) backend application.
- **`frontend/`**: Contains the manifests for the Next.js frontend application.
- **`payments/`**: Contains the manifests for the Go payments microservice.
- **`tools/`**: This is a crucial directory for local development. It contains the manifests to deploy all necessary third-party services and dependencies, such as:
    - MySQL
    - Elasticsearch
    - Redis
    - RabbitMQ

### `k8s/prod/`

This directory is intended to hold the manifests for the production deployment.
