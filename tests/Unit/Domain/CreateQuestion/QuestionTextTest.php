<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\CreateQuestion;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\CreateQuestion\QuestionText;

class QuestionTextTest extends TestCase
{
    /**
     * @test
     */
    public function it_fails_to_instantiate_with_an_empty_text(): void
    {
        $this->expectException(InvalidArgumentException::class);
        QuestionText::fromString('');
    }

    /**
     * @test
     */
    public function it_fails_to_instantiate_with_a_meaningless_text(): void
    {
        $this->expectException(InvalidArgumentException::class);
        QuestionText::fromString('     ');
    }
}
