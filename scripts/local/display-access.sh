MINIKUBE_IP=$(minikube ip)

echo "Minikube ip: $MINIKUBE_IP"
echo "Frontend: http://$MINIKUBE_IP:3000"
echo "Backend API: http://$MINIKUBE_IP:30080/api/v1"
echo "API Documentation: http://$MINIKUBE_IP:30080/api/doc"
echo "RabbitMQ Management: http://$MINIKUBE_IP:30672"
echo "MailHog: http://$MINIKUBE_IP:30225"
echo "Redis: $MINIKUBE_IP:30379"
