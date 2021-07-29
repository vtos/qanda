<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\AnswerQuestion\AnswerQuestion;
use App\Domain\AnswerQuestion\AnswerQuestionHandler;
use App\Domain\AnswerQuestion\CouldNotAnswerQuestion;
use App\Domain\CreateQuestion\CreateQuestion;
use App\Domain\CreateQuestion\CreateQuestionHandler;
use App\Domain\Stats\Percentage;
use App\Models\Question;
use App\Models\QuestionAttempt;
use Illuminate\Console\Command;

class QandaInteractive extends Command
{
    private const MAIN_MENU_CREATE_QUESTION_OPTION = 1;

    private const MAIN_MENU_LIST_QUESTIONS_OPTION = 2;

    private const MAIN_MENU_PRACTICE_OPTION = 3;

    private const MAIN_MENU_STATS_OPTION = 4;

    private const MAIN_MENU_RESET_OPTION = 5;

    private const MAIN_MENU_EXIT_OPTION = 6;

    private CreateQuestionHandler $createQuestionHandler;

    private AnswerQuestionHandler $answerQuestionHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A console command to run Q&A App.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        CreateQuestionHandler $createQuestionHandler,
        AnswerQuestionHandler $answerQuestionHandler
    ) {
        $this->createQuestionHandler = $createQuestionHandler;
        $this->answerQuestionHandler = $answerQuestionHandler;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $selectedOption = null;
        while ($selectedOption !== self::MAIN_MENU_EXIT_OPTION) {
            if ($selectedOption === null) {
                $this->line('Welcome to Q&A App!');
            }

            $options = $this->mainMenuOptions();
            $optionText = $this->choice(
                '<fg=white>Choose what you want to do</>',
                $options
            );
            $selectedOption = array_search($optionText, $options);

            if (self::MAIN_MENU_CREATE_QUESTION_OPTION === $selectedOption) {
                $this->handleCreateQuestion();
            }
            if (self::MAIN_MENU_LIST_QUESTIONS_OPTION === $selectedOption) {
                $this->handleListQuestions();
            }
            if (self::MAIN_MENU_PRACTICE_OPTION === $selectedOption) {
                $this->handlePractice();
            }
            if (self::MAIN_MENU_STATS_OPTION === $selectedOption) {
                $this->handleStats();
            }
            if (self::MAIN_MENU_RESET_OPTION === $selectedOption) {
                $this->handleReset();
            }
        }

        $this->info('Bye!');

        return 0;
    }

    /**
     * Provides array of options to output the main menu in console.
     *
     * @return string[]
     */
    private function mainMenuOptions(): array
    {
        return [
            self::MAIN_MENU_CREATE_QUESTION_OPTION => 'Create a question',
            self::MAIN_MENU_LIST_QUESTIONS_OPTION => 'List all questions',
            self::MAIN_MENU_PRACTICE_OPTION => 'Practice',
            self::MAIN_MENU_STATS_OPTION => 'Stats',
            self::MAIN_MENU_RESET_OPTION => 'Reset',
            self::MAIN_MENU_EXIT_OPTION => 'Exit',
        ];
    }

    private function handleCreateQuestion(): void
    {
        $questionText = $this->ask('<fg=white>Enter question text</>');
        $questionAnswer = $this->ask('<fg=white>Enter question answer</>');

        $this->createQuestionHandler->handle(
            new CreateQuestion($questionText, $questionAnswer)
        );
        $this->info('Added successfully.');
    }

    private function handleListQuestions(): void
    {
        $this->line('Q&A List');
        $this->table(
            [
                'Question Text',
                'Answer',
            ],
            Question::list()->get()
        );
    }

    private function handlePractice(): void
    {
        $this->line('Let\'s practice!');
        $this->newLine();
        $this->line('Current progress:');

        // TODO: put this into separate method.
        $questions = Question::answers()->get();

        $questionsAsArray = [];
        $questionsNumIdMap = [];
        $questionsNumTextMap = [];
        foreach ($questions as $question) {
            $questionNum = count($questionsAsArray) + 1;

            $questionsNumIdMap[$questionNum] = $question->id;
            $questionsNumTextMap[$questionNum] = $question->question_text;

            $questionsAsArray[] = [
                $questionNum,
                $question->question_text,
                $question->attempt->user_answer,
                $question->attempt->status,
            ];
        }

        $this->table(
            [
                'Question Num.',
                'Question',
                'Your Answer',
                'Status',
            ],
            $questionsAsArray
        );

        $questionNumToPractice = null;
        while (is_null($questionNumToPractice) || !array_key_exists($questionNumToPractice, $questionsNumIdMap)) {
            $questionNumToPractice = $this->ask(
                '<fg=white>Pick a question to practice (enter question number from the table above or press Enter to return to the main menu)</>'
            );
            if ($questionNumToPractice === null) {
                return;
            }
            if (!in_array($questionNumToPractice, array_keys($questionsNumIdMap))) {
                $this->error('Unknown question selected.');
            }
        }

        $questionAnswer = $this->ask(
            sprintf(
            '<fg=white>%s Type your answer</>',
                $questionsNumTextMap[$questionNumToPractice]
            )
        );

        try {
            $answerStatus = $this->answerQuestionHandler->handle(
                new AnswerQuestion(
                    $questionsNumIdMap[$questionNumToPractice],
                    $questionAnswer
                )
            );

            $this->info(
                sprintf(
                    '<fg=%s>The answer is %s.</>',
                    $answerStatus->isCorrect() ? 'green' : 'red',
                    $answerStatus->asString()
                )
            );
        } catch (CouldNotAnswerQuestion $couldNotAnswerQuestion) {
            $this->error($couldNotAnswerQuestion->getMessage());
        }
    }

    private function handleStats(): void
    {
        $this->line('Stats');

        $totalQuestions = Question::all()->count();

        $answeredPercentage = Percentage::fromInts(
            $totalQuestions,
            QuestionAttempt::answeredCount()->count()
        )->asInt();

        $correctPercentage = Percentage::fromInts(
            $totalQuestions,
            QuestionAttempt::correctCount()->count(),
        )->asInt();

        $this->table(
            [
                'Total number of questions',
                '% of questions with an answer',
                '% of questions with a correct answer',
            ],
            [
                [
                    $totalQuestions,
                    $answeredPercentage,
                    $correctPercentage,
                ]
            ]
        );
    }

    private function handleReset(): void
    {
        $typed = $this->ask('<fg=yellow>Are you sure you want to reset the progress?</> <fg=white>(type \'yes\' to confirm)</>');
        if ($typed !== 'yes') {
            $this->info('Reset aborted.');
            return;
        }
        $this->info('Deleted.');
    }
}
