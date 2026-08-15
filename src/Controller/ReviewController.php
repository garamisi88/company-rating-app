<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use App\Enum\ReviewSort;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    public function __construct(
        private readonly int $reviewsPageSize,
    ) {
    }

    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(Request $request, ReviewRepository $reviewRepository): Response
    {
        $sort = ReviewSort::fromString($request->query->get('sort', ''));
        $page = max(1, (int) $request->query->get('page', 1));
        $query = trim($request->query->get('q', ''));

        $paginator = $reviewRepository->paginate(
            $sort,
            $page,
            $this->reviewsPageSize,
            '' !== $query ? $query : null
        );
        $pageCount = max(1, (int) ceil(\count($paginator) / $this->reviewsPageSize));

        return $this->render('review/index.html.twig', [
            'reviews' => $paginator,
            'sort' => $sort,
            'sortOptions' => ReviewSort::cases(),
            'currentPage' => $page,
            'pageCount' => $pageCount,
            'query' => $query,
        ]);
    }

    #[Route('/review/{id}', name: 'app_review_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/review/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'review.flash.created');

            return $this->redirectToRoute('app_review_show', ['id' => $review->getId()]);
        }

        return $this->render('review/new.html.twig', [
            'form' => $form,
        ]);
    }
}
