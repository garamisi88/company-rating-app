<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\CompanyStats;
use App\Entity\Review;
use App\Enum\ReviewSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function paginate(
        ReviewSort $reviewSort,
        int $page,
        int $pageSize,
        ?string $companyName,
    ): Paginator {
        $builder = $this->createQueryBuilder('r')
            ->orderBy('r.'.$reviewSort->field(), $reviewSort->direction())
            ->addOrderBy('r.id', 'DESC')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if (null !== $companyName) {
            $this->applyCompanyNameFilter($builder, $companyName);
        }

        $query = $builder->getQuery();

        return new Paginator($query, false);
    }

    public function getCompanyStatistics(?string $companyName): array
    {
        $builder = $this->createQueryBuilder('r')
            ->select(
                'r.companyName AS companyName',
                'COUNT(r.id) AS reviewCount',
                'AVG(r.rating) AS averageRating',
                'SUM(CASE WHEN r.rating = 1 THEN 1 ELSE 0 END) AS rating1',
                'SUM(CASE WHEN r.rating = 2 THEN 1 ELSE 0 END) AS rating2',
                'SUM(CASE WHEN r.rating = 3 THEN 1 ELSE 0 END) AS rating3',
                'SUM(CASE WHEN r.rating = 4 THEN 1 ELSE 0 END) AS rating4',
                'SUM(CASE WHEN r.rating = 5 THEN 1 ELSE 0 END) AS rating5',
            )
            ->groupBy('r.companyName')
            ->orderBy('averageRating', 'DESC')
            ->addOrderBy('reviewCount', 'DESC')
            ->addOrderBy('r.companyName', 'ASC');

        if (null !== $companyName) {
            $this->applyCompanyNameFilter($builder, $companyName);
        }

        return array_map(
            CompanyStats::fromRow(...),
            $builder->getQuery()->getScalarResult()
        );
    }

    private function applyCompanyNameFilter(QueryBuilder $builder, ?string $companyName): void
    {
        if (null === $companyName || '' === $companyName) {
            return;
        }

        $pattern = addcslashes(mb_strtolower($companyName), '%_');

        $builder
            ->andWhere('LOWER(r.companyName) LIKE :companyName')
            ->setParameter('companyName', '%'.$pattern.'%');
    }
}
