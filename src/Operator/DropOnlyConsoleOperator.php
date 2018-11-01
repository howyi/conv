<?php

namespace Laminaria\Conv\Operator;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class DropOnlyConsoleOperator extends ConsoleOperator
{
    /**
     * @param string $message
     * @param array  $choices
     * @return mixed
     */
    public function choiceQuestion(string $message, array $choices)
    {
        if (in_array('dropped', $choices, true))
        {
        	$this->output('-> dropped');
            return 'dropped';
        }
        throw new \RuntimeException('"dropped" answer not exist.');
    }
}
