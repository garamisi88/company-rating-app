<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CompanyStats
{
    public function __construct(
        public string $companyName,
        public int $reviewCount,
        public float $averageRating,
        public array $distributions,
    ) {
    }

    public function countFor(int $rating): int
    {
        return $this->distributions[$rating] ?? 0;
    }

    public function percentageFor(int $rating): float
    {
        if (0 === $this->reviewCount) {
            return 0.0;
        }

        return round($this->countFor($rating) / $this->reviewCount * 100, 1);
    }

    public static function fromRow(array $row): self
    {
        $distributions = [];

        for ($rating = 1; $rating < 6; ++$rating) {
            $distributions[$rating] = (int) $row['rating'.$rating];
        }

        return new self(
            (string) $row['companyName'],
            (int) $row['reviewCount'],
            (float) $row['averageRating'],
            $distributions
        );
    }
}
