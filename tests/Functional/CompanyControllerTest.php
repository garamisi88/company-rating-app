<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanyControllerTest extends WebTestCase
{
    public function testCompaniesAreRenderedInDescendingAverageOrder(): void
    {
        $client = static::createClient();

        $this->createReviews('Teszt Kft.', [5, 5, 4]);
        $this->createReviews('Profi Bt.', [4, 4]);
        $this->createReviews('Béna Béla', [2, 1]);

        $crawler = $client->request('GET', '/companies');

        $this->assertResponseIsSuccessful();
        $names = $crawler->filter('.card h2')->each(static fn ($node): string => trim($node->text()));

        $this->assertSame(['Teszt Kft.', 'Profi Bt.', 'Béna Béla'], $names);
    }

    public function testTheSearchFormFiltersCompanies(): void
    {
        $client = static::createClient();

        $this->createReviews('Teszt Kft.', [5, 4, 4]);
        $this->createReviews('Pacal Kft.', [3]);

        $crawler = $client->request('GET', '/companies?q=Teszt');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.card h2'));
        $this->assertSelectorTextContains('.card h2', 'Teszt Kft.');
        $this->assertSelectorTextContains('.card', '4,3');
    }

    private function createReviews(string $companyName, array $ratings): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        foreach ($ratings as $rating) {
            $entityManager->persist(
                (new Review())
                    ->setCompanyName($companyName)
                    ->setRating($rating)
                    ->setReviewText('Teszteset által létrehozott vélemény.')
                    ->setAuthorEmail('teszt@example.com')
            );
        }

        $entityManager->flush();
    }
}
