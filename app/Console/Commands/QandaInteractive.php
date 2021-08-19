<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\Table;
use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Domain\AnswerQuestion\AnswerQuestion;
use App\Domain\AnswerQuestion\AnswerQuestionHandler;
use App\Domain\AnswerQuestion\CouldNotAnswerQuestion;
use App\Domain\CreateQuestion\CreateQuestion;
use App\Domain\CreateQuestion\CreateQuestionHandler;
use App\Domain\Stats\Percentage;

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

            // For some reason when running tests Laravel handles this output differently,
            // the choice()'s output contains the selected option itself, not the option text.
            // That's why handling for both cases.
            $selectedOption = array_key_exists((int)$optionText, $options)
                ? (int)$optionText
                : array_search($optionText, $options);

            if (self::MAIN_MENU_CREATE_QUESTION_OPTION === $selectedOption) {
                $this->handleCreateQuestion();
            }
            if (self::MAIN_MENU_LIST_QUESTIONS_OPTION === $selectedOption) {
                $this->handleListQuestions();
            }
            if (self::MAIN_MENU_PRACTICE_OPTION === $selectedOption) {
                // Keep showing the practice dialog until explicitly asked to stop (when 0 is returned)
                while (1 === $this->handlePractice());
            }
            if (self::MAIN_MENU_STATS_OPTION === $selectedOption) {
                $this->handleStats();
            }
            if (self::MAIN_MENU_RESET_OPTION === $selectedOption) {
                $this->handleReset();
            }
        }

        $this->info('Bye!');
        $this->newLine();

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
        $questionText = $this->ask('<fg=white>Enter question text or press Enter to cancel</>');
        if (is_null($questionText)) {
            return;
        }

        $questionAnswer = null;
        while (is_null($questionAnswer)) {
            $questionAnswer = $this->ask('<fg=white>Enter question answer</>');
            if (is_null($questionAnswer)) {
                $this->error('Please, provide the answer.');
            }
        }

        $this->createQuestionHandler->handle(
            new CreateQuestion($questionText, $questionAnswer)
        );
        $this->info('Added successfully.');
        $this->newLine();
    }

    private function handleListQuestions(): void
    {
        $questions =Question::list()->get();
        if ($questions->isEmpty()) {
            $this->line('No questions added yet.');
            $this->newLine();

            return;
        }

        $this->line('Q&A List');
        $this->table(
            [
                'Question Text',
                'Answer',
            ],
            $questions
        );
    }

    /**
     * The method returns either 0 or 1. In case the user wants to exit the practice it returns 0. If 1 is returned
     * the user is presented the practice again.
     *
     * @return int
     */
    private function handlePractice(): int
    {
        $questionsCollection = Question::withAttempts()->get();
        if ($questionsCollection->isEmpty()) {
            $this->line('No questions added yet, nothing to practice, sorry.');
            $this->newLine();

            // Go to main menu.
            return 0;
        }

        $this->line('Let\'s practice!');
        $this->newLine();
        $this->line('Current progress:');

        // This map is needed to match the selected question number with its id
        // which is required for further manipulations.
        $questionsNumIdMap = $questionsCollection->mapWithKeys(function($item, $key) {
            return [
                ($key + 1) => $item->id,
            ];
        })->toArray();

        // Get questions in a form which is convenient for passing to the progressTable() method down below.
        $questions = $questionsCollection->map(function($item, $key) {
            return [
                $key + 1,
                $item->question_text,
                $item->attempt->user_answer,
                Str::ucfirst(
                    Str::replace('_', ' ', $item->attempt->status->asString())
                )
            ];
        })->all();

        $totalQuestions = Question::all()->count();
        $correctAnswersTotal = QuestionAttempt::whereHasCorrectAnswer()->count();
        $this->progressTable(
            $questions,
            sprintf(
                '%s%% (%s / %s)',
                Percentage::fromInts($totalQuestions, $correctAnswersTotal)->asRoundedInt(),
                $correctAnswersTotal,
                $totalQuestions
            )
        );

        $questionNumToPractice = null;
        $hasCorrectAnswerAlready = false;
        while (
            is_null($questionNumToPractice)
            || !array_key_exists($questionNumToPractice, $questionsNumIdMap)
            || $hasCorrectAnswerAlready
        ) {
            $questionNumToPractice = $this->ask(
                '<fg=white>Pick a question to practice (enter question number from the table above or press Enter to return to the main menu)</>'
            );
            if (is_null($questionNumToPractice)) {
                // Cancel practice.
                return 0;
            }

            if (!in_array($questionNumToPractice, array_keys($questionsNumIdMap))) {
                $this->error('Unknown question selected.');
                continue;
            }

            $questionToPractice = Question::with('attempt')->findOrFail($questionsNumIdMap[$questionNumToPractice]);
            $hasCorrectAnswerAlready = $questionToPractice->attempt->hasCorrectAnswer();
            if ($hasCorrectAnswerAlready) {
                $this->error('The question has a correct answer already, pick another one, please.');
            }
        }

        $questionAnswer = $this->ask(
            sprintf(
            '<fg=white>%s Type your answer or press Enter to skip</>',
                $questionToPractice->question_text
            )
        );
        if (is_null($questionAnswer)) {
            // Skip question and return to practice progress.
            return 1;
        }

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

        $this->newLine();

        return 1;
    }

    private function handleStats(): void
    {
        $this->line('Stats');

        $totalQuestions = Question::count();

        $answeredPercentage = Percentage::fromInts(
            $totalQuestions,
            QuestionAttempt::whereAnswered()->count()
        )->asRoundedInt();

        $correctPercentage = Percentage::fromInts(
            $totalQuestions,
            QuestionAttempt::whereHasCorrectAnswer()->count(),
        )->asRoundedInt();

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

        QuestionAttempt::truncate();
        $this->info('Deleted the progress.');
    }

    /**
     * Overriding the basic table to have a beautiful footer with the progress data.
     */
    private function progressTable(array $rows, string $footer): void
    {
        $table = new Table($this->output);
        $table->setHeaders(
            [
                'Question Num.',
                'Question',
                'Your Answer',
                'Status',
            ]
        );
        $table->setRows($rows);
        $table->setFooterTitle($footer);

        $table->render();
    }
}
