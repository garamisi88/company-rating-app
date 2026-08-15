<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enum;

use App\Enum\ReviewSort;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReviewSortTest extends TestCase
{
    public function testUnknownValuesFallBackToTheDefault(): void
    {
        $this->assertSame(ReviewSort::Newest, ReviewSort::fromString('unknown'));
        $this->assertSame(ReviewSort::Newest, ReviewSort::fromString(''));
    }

    public function testKnownValuesResolveToTheirCase(): void
    {
        $this->assertSame(ReviewSort::Newest, ReviewSort::fromString('newest'));
        $this->assertSame(ReviewSort::Oldest, ReviewSort::fromString('oldest'));
        $this->assertSame(ReviewSort::Highest, ReviewSort::fromString('highest'));
        $this->assertSame(ReviewSort::Lowest, ReviewSort::fromString('lowest'));
    }

    #[DataProvider('orderings')]
    public function testCaseMapsToOrdering(
        ReviewSort $reviewSort,
        string $expectedField,
        string $expectedDirection,
    ): void {
        $this->assertSame($expectedField, $reviewSort->field());
        $this->assertSame($expectedDirection, $reviewSort->direction());
    }

    public static function orderings(): array
    {
        return [
            [ReviewSort::Newest, 'createdAt', 'DESC'],
            [ReviewSort::Oldest, 'createdAt', 'ASC'],
            [ReviewSort::Highest, 'rating', 'DESC'],
            [ReviewSort::Lowest, 'rating', 'ASC'],
        ];
    }
}
