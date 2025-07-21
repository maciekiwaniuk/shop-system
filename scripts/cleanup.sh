#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_status "🧹 Cleaning up Shop System Kubernetes resources..."

# Check if namespace exists
if kubectl get namespace shop-system >/dev/null 2>&1; then
    print_status "🗑️  Deleting all resources in shop-system namespace..."

    # Delete all resources in the namespace
    kubectl delete all --all -n shop-system
    kubectl delete pvc --all -n shop-system
    kubectl delete configmap --all -n shop-system

    # Delete the namespace itself
    kubectl delete namespace shop-system

    print_success "✅ All shop-system resources deleted successfully"
else
    print_warning "Namespace shop-system does not exist or is already deleted"
fi

print_status "🎯 Cleanup completed!"