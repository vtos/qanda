<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\CreateQuestion\CreateQuestion;
use App\Domain\CreateQuestion\CreateQuestionHandler;
use App\Domain\ListQuestions\GetQuestions;
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

    private GetQuestions $getQuestions;

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
        GetQuestions $getQuestions
    ) {
        $this->createQuestionHandler = $createQuestionHandler;
        $this->getQuestions = $getQuestions;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // TODO: use white color for text as default.
        $this->line('<fg=white>Welcome to Q&A App!</>');

        // TODO: check if can be solved without array_search().

        $options = $this->mainMenuOptions();
        $optionText = $this->choice(
            '<fg=white>Choose what you want to do</>',
            $options
        );
        $optionValue = array_search($optionText, $options);

        if (self::MAIN_MENU_CREATE_QUESTION_OPTION === $optionValue) {
            $createQuestionInput = $this->createQuestionInput();

            $this->createQuestionHandler->handle(
                new CreateQuestion(
                    $createQuestionInput['question_text'],
                    $createQuestionInput['question_answer']
                )
            );
            $this->line('Added successfully.');
            return 0;
        }

        if (self::MAIN_MENU_LIST_QUESTIONS_OPTION === $optionValue) {
            $this->line('<fg=white>Q&A List</>');
            $this->table(
                [
                    'Questions',
                    'Answers',
                ],
                $this->getQuestions->all()->toArray()
            );

            return 0;
        }

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

    /**
     * Asks a user for question text and its answer in console and returns the input.
     *
     * @return array Array of two strings: question text and question answer,
     * the keys are 'question_text' and 'question_answer' respectively.
     */
    private function createQuestionInput(): array
    {
        $questionText = $this->ask('Enter question text:');
        $questionAnswer = $this->ask('Enter question answer:');

        return [
            'question_text' => $questionText,
            'question_answer' => $questionAnswer,
        ];
    }
}
