<?php

namespace Laminaria\Conv\Operator;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ConsoleOperator implements OperatorInterface
{
    /**
     * @var QuestionHelper
     */
    protected $helper;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ProgressBar|null
     */
    protected $progress;

    /**
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(
        QuestionHelper $helper,
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->helper = $helper;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param string $message
     * @param array  $choices
     * @return mixed
     */
    public function choiceQuestion(string $message, array $choices)
    {
        $question = new ChoiceQuestion($message, $choices);
        return $this->helper->ask($this->input, $this->output, $question);
    }

    /**
     * @param string $message
     */
    public function output(string $message): void
    {
       $this->output->writeln($message);
    }

    /**
     * @param int $max
     */
    public function startProgress(int $max): void
    {
        $this->progress = new ProgressBar($this->output, $max);
        $this->progress->start();
    }

    /**
     * @param int $step
     */
    public function advanceProgress(int $step = 1): void
    {
        $this->progress->advance($step);
    }

    public function setProgressFormat(string $format): void
    {
        $this->progress->setFormat($format);
    }

    public function finishProgress(string $message): void
    {
        $this->progress->finish();
		$this->output->writeln($message);
    }
}
