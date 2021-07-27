<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AnswerQuestion;

use App\Domain\AnswerQuestion\AnswerStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AnswerStatusTest extends TestCase
{
    /**
     * @test
     */
    public function it_fails_to_instantiate_with_an_invalid_status_option(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AnswerStatus::fromString('none');
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_valid_string(): void
    {
        $this->assertEquals(
            AnswerStatus::notAnswered(),
            AnswerStatus::fromString('not_answered')
        );

        $this->assertEquals(
            AnswerStatus::incorrect(),
            AnswerStatus::fromString('incorrect')
        );

        $this->assertEquals(
            AnswerStatus::correct(),
            AnswerStatus::fromString('correct')
        );
    }

    /**
     * @test
     */
    public function it_can_tell_if_the_status_is_correct(): void
    {
        $this->assertTrue(
            AnswerStatus::correct()->isCorrect()
        );

        $this->assertFalse(
            AnswerStatus::incorrect()->isCorrect()
        );

        $this->assertFalse(
            AnswerStatus::notAnswered()->isCorrect()
        );
    }
}
