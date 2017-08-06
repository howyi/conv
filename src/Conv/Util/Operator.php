<?php

namespace Conv\Util;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Operator
{
    private $helper;
    private $input;
    private $output;

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
    public function output(string $message)
    {
        return $this->output->writeln($message);
    }
}
