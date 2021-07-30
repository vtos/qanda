<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Stats;

use PHPUnit\Framework\TestCase;
use App\Domain\Stats\Percentage;

class PercentageTest extends TestCase
{
    /**
     * @test
     */
    public function it_fails_to_instantiate_with_a_negative_total(): void
    {
        // Aim for specific exception message to distinguish the cause of exception.
        $this->expectExceptionMessage('Total cannot be negative.');
        Percentage::fromInts(-1, 0);
    }

    /**
     * @test
     */
    public function it_fails_to_instantiate_with_a_negative_amount(): void
    {
        // Aim for specific exception message to distinguish the cause of exception.
        $this->expectExceptionMessage('Amount cannot be negative.');
        Percentage::fromInts(2, -2);
    }

    /**
     * @test
     */
    public function it_fails_to_instantiate_when_amount_is_greater_than_total(): void
    {
        // Aim for specific exception message to distinguish the cause of exception.
        $this->expectExceptionMessage('Amount cannot be greater than total.');
        Percentage::fromInts(2, 4);
    }

    /**
     * @test
     */
    public function it_returns_percentage_as_a_rounded_int(): void
    {
        $percentage = Percentage::fromInts(7, 2);
        $this->assertEquals(
            $percentage->asRoundedInt(),
            29
        );

        $percentage = Percentage::fromInts(0, 0);
        $this->assertEquals(
            $percentage->asRoundedInt(),
            0
        );

        $percentage = Percentage::fromInts(4, 0);
        $this->assertEquals(
            $percentage->asRoundedInt(),
            0
        );

        $percentage = Percentage::fromInts(4, 4);
        $this->assertEquals(
            $percentage->asRoundedInt(),
            100
        );
    }
}
