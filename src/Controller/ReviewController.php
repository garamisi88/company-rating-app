<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use App\Enum\ReviewSort;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    public function __construct(
        private readonly int $reviewsPageSize
    ) {
    }

    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(Request $request, ReviewRepository $reviewRepository): Response
    {
        $sort = ReviewSort::fromString($request->query->get('sort', ''));
        $page = max(1, (int) $request->query->get('page', 1));

        $paginator = $reviewRepository->paginate($sort, $page, $this->reviewsPageSize);
        $pageCount = max(1, (int) ceil(\count($paginator) / $this->reviewsPageSize));

        return $this->render('review/index.html.twig', [
            'reviews' => $paginator,
            'sort' => $sort,
            'sortOptions' => ReviewSort::cases(),
            'currentPage' => $page,
            'pageCount' => $pageCount,
        ]);
    }

    #[Route('/review/{id}', name: 'app_review_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }
}
