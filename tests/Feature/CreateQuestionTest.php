<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Question;
use App\Domain\CreateQuestion\CreateQuestion;
use App\Domain\CreateQuestion\CreateQuestionHandler;

class CreateQuestionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_create_a_question(): void
    {
        $questionText = 'Some question?';
        $questionAnswer = 'The answer';

        (new CreateQuestionHandler())->handle(
            new CreateQuestion($questionText, $questionAnswer)
        );

        $this->assertEquals(
            1,
            Question::where(
                [
                    'question_text' => $questionText,
                    'question_answer' => $questionAnswer
                ]
            )->count()
        );
    }
}
