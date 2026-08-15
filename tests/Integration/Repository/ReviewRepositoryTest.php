<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReviewRepositoryTest extends KernelTestCase
{
    public function testTheKernelBootsAgainstTheTestDatabase(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $review = (new Review())
            ->setCompanyName('Teszt Elek Bt.')
            ->setRating(5)
            ->setReviewText('Ez a legjobb cég, amivel valaha találkoztam.')
            ->setAuthorEmail('hello@tesztelek.hu');

        $entityManager->persist($review);
        $entityManager->flush();

        $this->assertNotNull($review->getId());
    }
}
