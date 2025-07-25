#!/bin/bash
set -e

echo "🚀 Deploying Shop System Backend to Kubernetes..."

if ! minikube status | grep -q "Running"; then
    echo "❌ Minikube is not running. Starting minikube..."
    minikube start --driver=docker
    minikube addons enable storage-provisioner
fi

MINIKUBE_IP=$(minikube ip)
echo "📍 Minikube IP: $MINIKUBE_IP"

echo "📦 Creating namespace..."
kubectl apply -f ../k8s/local/namespace.yaml

echo "💾 Deploying storage..."
kubectl apply -f ../k8s/local/storage/

echo "🔧 Deploying configmaps..."
kubectl apply -f ../k8s/local/configmaps/

echo "🔧 Deploying secrets..."
kubectl apply -f ../k8s/local/secrets/

echo "🔧 Deploying services..."
kubectl apply -f ../k8s/local/services/

echo "🚀 Deploying applications..."
kubectl apply -f ../k8s/local/app/

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