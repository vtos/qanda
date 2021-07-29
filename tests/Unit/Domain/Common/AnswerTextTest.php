<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Common;

use App\Domain\Common\AnswerText;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AnswerTextTest extends TestCase
{
    /**
     * @test
     */
    public function it_fails_to_instantiate_with_an_empty_text(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AnswerText::fromString('');
    }

    /**
     * @test
     */
    public function it_fails_to_instantiate_with_a_meaningless_text(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AnswerText::fromString('     ');
    }

    /**
     * @test
     */
    public function it_can_assess_equality_with_another_answer_text(): void
    {
        $answerText = AnswerText::fromString('Some answer.');

        $this->assertTrue(
            $answerText->equals(
                AnswerText::fromString('Some answer.')
            )
        );

        $this->assertFalse(
            $answerText->equals(
                AnswerText::fromString('Another answer.')
            )
        );
    }
}
