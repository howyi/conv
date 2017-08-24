<?php

namespace Conv;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class OperatorTest extends \PHPUnit\Framework\TestCase
{
    private $prophet;

    protected function setup()
    {
        $this->prophet = new \Prophecy\Prophet;
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    public function testConstruct()
    {
        $helper = $this->prophet->prophesize(QuestionHelper::class);
        $input = $this->prophet->prophesize(InputInterface::class);
        $output = $this->prophet->prophesize(OutputInterface::class);
        $operator = new Operator($helper->reveal(), $input->reveal(), $output->reveal());

        $answer = 'answer';
        $helper->ask($input, $output, \Prophecy\Argument::type(ChoiceQuestion::class))
            ->willReturn($answer)
            ->shouldBeCalledTimes(1);
        $this->assertSame($answer, $operator->choiceQuestion('question', ['answer']));

        $message = 'message';
        $output->writeln($message)->shouldBeCalledTimes(1);
        $operator->output($message);
    }
}
