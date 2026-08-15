<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompanyController extends AbstractController
{
    #[Route('/companies', name: 'app_company_index', methods: ['GET'])]
    public function index(Request $request, ReviewRepository $reviewRepository): Response
    {
        $query = trim($request->query->get('q', ''));

        return $this->render('company/index.html.twig', [
            'companies' => $reviewRepository->getCompanyStatistics('' !== $query ? $query : null),
            'query' => $query,
        ]);
    }
}
