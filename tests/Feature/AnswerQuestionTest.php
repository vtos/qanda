<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Domain\AnswerQuestion\AnswerQuestion;
use App\Domain\AnswerQuestion\AnswerQuestionHandler;
use App\Domain\AnswerQuestion\AnswerStatus;
use App\Domain\AnswerQuestion\CouldNotAnswerQuestion;

class AnswerQuestionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_answer_question(): void
    {
        $correctAnswerText = 'Known answer';
        $incorrectAnswerText = 'Some incorrect answer';

        $question = Question::factory()->state(
            [
                'question_answer' => $correctAnswerText,
            ]
        )->create();

        // Give incorrect answer first.
        (new AnswerQuestionHandler())->handle(
            new AnswerQuestion($question->id, $incorrectAnswerText)
        );

        $questionAttempts = QuestionAttempt::where('question_id', $question->id)->get();

        // Check only one record is created.
        $this->assertEquals(1, $questionAttempts->count());

        $questionAttempt = $questionAttempts->first();

        // Check answer text is stored.
        $this->assertEquals($questionAttempt->user_answer, $incorrectAnswerText);

        // The status.
        $this->assertEquals(
            AnswerStatus::incorrect(),
            $questionAttempt->status
        );

        // Now the correct answer.
        (new AnswerQuestionHandler())->handle(
            new AnswerQuestion($question->id, $correctAnswerText)
        );

        $questionAttempts = QuestionAttempt::where('question_id', $question->id)->get();

        // Check it's still only one record.
        $this->assertEquals(1, $questionAttempts->count());

        $questionAttempt = $questionAttempts->first();

        // Check answer text is updated.
        $this->assertEquals($questionAttempt->user_answer, $correctAnswerText);

        // And the status.
        $this->assertEquals(
            AnswerStatus::correct(),
            $questionAttempt->status
        );
    }

    /**
     * @test
     */
    public function user_cannot_answer_the_question_which_is_answered_correctly_already(): void
    {
        $correctAnswer = 'The answer';
        $questionAttempt = QuestionAttempt::factory()
            ->state(
                [
                    'user_answer' => $correctAnswer,
                    'status' => AnswerStatus::correct()->asString(),
                ]
            )->forQuestion(
                [
                    'question_text' => 'Some question?',
                    'question_answer' => $correctAnswer,
                ]
            )->create();

        $this->expectException(CouldNotAnswerQuestion::class);
        (new AnswerQuestionHandler())->handle(
            new AnswerQuestion($questionAttempt->question_id, 'Some answer.')
        );
    }
}
