<?php

declare(strict_types=1);

namespace App\Enum;

enum ReviewSort: string
{
    case Newest = 'newest';
    case Oldest = 'oldest';
    case Highest = 'highest';
    case Lowest = 'lowest';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Newest;
    }

    public function field(): string
    {
        return match ($this) {
            self::Newest, self::Oldest => 'createdAt',
            self::Highest, self::Lowest => 'rating',
        };
    }

    public function direction(): string
    {
        return match ($this) {
            self::Newest, self::Highest => 'DESC',
            self::Oldest, self::Lowest => 'ASC',
        };
    }

    public function getTranslationKey(): string
    {
        return 'review.sort.'.$this->value;
    }
}
