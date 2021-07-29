<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AnswerQuestion;

use PHPUnit\Framework\TestCase;
use App\Domain\Common\AnswerText;
use App\Domain\AnswerQuestion\AnswerStatus;
use App\Domain\AnswerQuestion\CouldNotAnswerQuestion;
use App\Domain\AnswerQuestion\Question;

class QuestionTest extends TestCase
{
    /**
     * @test
     */
    public function it_cannot_be_answered_if_has_a_correct_answer_already(): void
    {
        $question = new Question(
            AnswerText::fromString('Some answer which is correct.'),
            AnswerStatus::correct()
        );

        $this->expectException(CouldNotAnswerQuestion::class);
        $question->answer(AnswerText::fromString('Some answer.'));
    }

    /**
     * @test
     */
    public function it_properly_changes_answer_status_when_it_is_answered(): void
    {
        $question = new Question(
            AnswerText::fromString('The correct answer.'),
            AnswerStatus::notAnswered()
        );

        // Fail it.
        $question->answer(
            AnswerText::fromString('Some wrong answer.')
        );
        $this->assertEquals(
            $question->status(),
            AnswerStatus::incorrect()
        );

        // Let's rematch.
        $question->answer(
            AnswerText::fromString('The correct answer.')
        );
        $this->assertEquals(
            $question->status(),
            AnswerStatus::correct()
        );
    }
}
