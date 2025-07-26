#!/bin/bash
set -e

echo "Deploying system to kubernetes..."

if ! minikube status | grep -q "Running"; then
    echo "Minikube is not working. Starting minikube..."
    minikube start --driver=docker
    minikube addons enable storage-provisioner
fi

MINIKUBE_IP=$(minikube ip)
echo "Minikube IP: $MINIKUBE_IP"

cd ../../k8s/local

echo "Creating namespace..."
kubectl apply -f namespace.yaml

echo "Deploying storage..."
kubectl apply -f storage/

echo "Deploying configmaps..."
kubectl apply -f configmaps/

echo "Deploying secrets..."
kubectl apply -f secrets/

echo "Deploying services..."
kubectl apply -f services/

echo "Deploying applications..."
kubectl apply -f app/

cd ../../scripts/local

echo "Wait 10 seconds for all pods to initialize"
sleep 10

echo "Running scripts..."
./init-mysql.sh
./init-backend.sh

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
