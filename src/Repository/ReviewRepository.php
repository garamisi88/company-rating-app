<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use App\Enum\ReviewSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function paginate(ReviewSort $reviewSort, int $page, int $pageSize): Paginator
    {
        $query = $this->createQueryBuilder('r')
            ->orderBy('r.'.$reviewSort->field(), $reviewSort->direction())
            ->addOrderBy('r.id', 'DESC')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery();

        return new Paginator($query, false);
    }
}
