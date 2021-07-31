<?php

declare(strict_types=1);

namespace Tests\EndToEnd;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConsoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_creates_a_question(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsOutput('Welcome to Q&A App!')
            ->expectsQuestion('<fg=white>Choose what you want to do</>', '1')
            ->expectsQuestion('<fg=white>Enter question text or press Enter to cancel</>', 'Some question?')
            ->expectsQuestion('<fg=white>Enter question answer</>', 'The answer')
            ->expectsOutput('Added successfully.')
            // Now go and see it in the list.
            ->expectsQuestion('<fg=white>Choose what you want to do</>', '2')
            ->expectsOutput('Q&A List')
            ->expectsTable(
                [
                    'Question Text',
                    'Answer',
                ],
                [
                    ['Some question?', 'The answer']
                ]
            )
            // See it in the stats may be?
            ->expectsQuestion('<fg=white>Choose what you want to do</>', '4')
            ->expectsOutput('Stats')
            ->expectsTable(
                [
                    'Total number of questions',
                    '% of questions with an answer',
                    '% of questions with a correct answer',
                ],
                [
                    ['1', '0', '0']
                ]
            )
            // Now exit.
            ->expectsQuestion('<fg=white>Choose what you want to do</>', '6')
            ->expectsOutput('Bye!')
            ->assertExitCode(0);
    }
}
