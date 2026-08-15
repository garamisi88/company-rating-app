<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto;

use App\Dto\CompanyStats;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompanyStatsTest extends TestCase
{
    public function testExposesTheNumberOfReviewsPerRating(): void
    {
        $stats = $this->getDefaultCompanyStats();

        $this->assertSame(2, $stats->countFor(5));
        $this->assertSame(0, $stats->countFor(1));
    }

    public function testReturnsZeroForARatingThatHasNoReviews(): void
    {
        $stats = new CompanyStats('Teszt Kft.', 1, 5.0, [5 => 1]);

        $this->assertSame(0, $stats->countFor(3));
    }

    public function testRoundsPercentagesToOneDecimal(): void
    {
        $stats = new CompanyStats('Teszt Kft.', 3, 2.0, [1 => 1, 2 => 1, 3 => 1]);

        $this->assertSame(33.3, $stats->percentageFor(2));
    }

    public function testBuildsFromAScalarDatabaseRow(): void
    {
        $stats = CompanyStats::fromRow([
            'companyName' => 'Teszt Kft.',
            'reviewCount' => '4',
            'averageRating' => '4.2500',
            'rating1' => '0',
            'rating2' => '0',
            'rating3' => '1',
            'rating4' => '1',
            'rating5' => '2',
        ]);

        $this->assertSame('Teszt Kft.', $stats->companyName);
        $this->assertSame(4, $stats->reviewCount);
        $this->assertSame(4.25, $stats->averageRating);
        $this->assertSame(2, $stats->countFor(5));
    }

    #[DataProvider('percentages')]
    public function testCalculatePercentageOfRating(int $rating, float $expectedResult): void
    {
        $stats = $this->getDefaultCompanyStats();

        $this->assertSame($expectedResult, $stats->percentageFor($rating));
    }

    public static function percentages(): array
    {
        return [
            'half of the the reviews' => [5, 50.0],
            'a quarter of the reviews' => [3, 25.0],
            'no reviews at all' => [2, 0.0],
        ];
    }

    private function getDefaultCompanyStats(): CompanyStats
    {
        return new CompanyStats(
            'Teszt Kft.',
            4,
            4.25,
            [1 => 0, 2 => 0, 3 => 1, 4 => 1, 5 => 2]
        );
    }
}
