MINIKUBE_IP=$(minikube ip)

echo "Minikube ip: $MINIKUBE_IP"
echo "Frontend: http://$MINIKUBE_IP:30080"
echo "Backend API: http://$MINIKUBE_IP:30081/api/v1/health"
echo "API Documentation: http://$MINIKUBE_IP:30081/api/doc"
echo "Payments API: http://$MINIKUBE_IP:30082/api/v1/health"
echo "RabbitMQ Management: http://$MINIKUBE_IP:30672"
echo "MailHog: http://$MINIKUBE_IP:30225"
echo "Redis: $MINIKUBE_IP:30379"
