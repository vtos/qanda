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
        }

        if (self::MAIN_MENU_LIST_QUESTIONS_OPTION === $optionValue) {
            $this->line('<fg=white>Q&A List</>');
            $this->table(
                [
                    'Question Text',
                    'Answer',
                ],
                Question::list()->get()
            );

        }

        if (self::MAIN_MENU_PRACTICE_OPTION === $optionValue) {
            $this->line('<fg=white>Let\'s practice!</>');
            $this->newLine();
            $this->line('<fg=white>Current progress:</>');

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
                    '<fg=white>Pick a question to practice (enter question number from the table above)</>'
                );
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

        if (self::MAIN_MENU_STATS_OPTION === $optionValue) {
            $this->line('<fg=white>Stats</>');

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

            $options = $this->mainMenuOptions();
            $optionText = $this->choice(
                '<fg=white>Choose what you want to do</>',
                $options
            );
            $optionValue = array_search($optionText, $options);
        }

        if (self::MAIN_MENU_RESET_OPTION === $optionValue) {
            $typed = $this->ask('Are you sure you want to reset the progress (type \'yes\' to confirm)?');
            if ($typed === 'yes') {

            }

            $options = $this->mainMenuOptions();
            $optionText = $this->choice(
                '<fg=white>Choose what you want to do</>',
                $options
            );
        }

        if (self::MAIN_MENU_EXIT_OPTION === $optionValue) {
            $this->info('Bye!');

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
