<?php

declare(strict_types=1);

namespace App\Common\Interface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class HealthController extends AbstractController
{
    #[Route('/health', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->json([
            'success' => true,
            'message' => 'Backend is working',
        ]);
    }
}
