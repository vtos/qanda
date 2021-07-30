<?php

declare(strict_types=1);

namespace App\Domain\Stats;

use Webmozart\Assert\Assert;

final class Percentage
{
    private int $total;

    private int $amount;

    private function __construct(int $total, int $amount)
    {
        Assert::greaterThanEq($total, 0, 'Total cannot be negative.');
        Assert::greaterThanEq($amount, 0, 'Amount cannot be negative.');
        Assert::lessThanEq($amount, $total, 'Amount cannot be greater than total.');

        $this->total = $total;
        $this->amount = $amount;
    }

    public function asRoundedInt(): int {
        if (0 === $this->amount || 0 === $this->total) {
            return 0;
        }

        return (int)round(
            $this->amount * 100 / $this->total
        );
    }

    public static function fromInts(int $total, int $amount): self
    {
        return new self($total, $amount);
    }
}
