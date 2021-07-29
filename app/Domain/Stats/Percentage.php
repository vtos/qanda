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
        Assert::nullOrPositiveInteger($total);
        Assert::nullOrPositiveInteger($amount);
        Assert::lessThanEq($amount, $total);

        $this->total = $total;
        $this->amount = $amount;
    }

    public function asInt(): int {
        return (int)ceil(
            $this->amount * 100 / $this->total
        );
    }

    public static function fromInts(int $total, int $amount): self
    {
        return new self($total, $amount);
    }
}
