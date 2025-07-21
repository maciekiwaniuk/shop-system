#!/bin/bash
set -e

echo "🚀 Deploying Shop System Backend to Kubernetes..."

# Check if minikube is running
if ! minikube status | grep -q "Running"; then
    echo "❌ Minikube is not running. Starting minikube..."
    minikube start --driver=docker
    minikube addons enable storage-provisioner
fi

# Get minikube IP
MINIKUBE_IP=$(minikube ip)
echo "📍 Minikube IP: $MINIKUBE_IP"

# Create namespace
echo "📦 Creating namespace..."
kubectl apply -f ../k8s/namespace.yaml

# Deploy storage first
echo "💾 Deploying storage..."
kubectl apply -f ../k8s/storage/

# Deploy services
echo "🔧 Deploying services..."
kubectl apply -f ../k8s/services/

# Deploy applications
echo "🚀 Deploying applications..."
kubectl apply -f ../k8s/app/

echo "✅ Deployment completed!"
echo ""
echo "🌐 Access your application:"
echo "   Backend API: http://$MINIKUBE_IP:30080"
echo "   API Endpoints: http://$MINIKUBE_IP:30080/api/v1/"
echo "   MailHog: http://$MINIKUBE_IP:30225"
echo "   RabbitMQ Management: http://$MINIKUBE_IP:30672"
echo ""
echo "📊 Check deployment status:"
echo "   kubectl get pods -n shop-system"
echo ""
echo "🧪 Test the API:"
echo "   curl http://$MINIKUBE_IP:30080/api/v1/register" 