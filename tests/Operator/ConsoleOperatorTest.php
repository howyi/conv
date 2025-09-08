<?php

namespace Howyi\Conv\Operator;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ConsoleOperatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Prophecy\Prophet
     */
    private $prophet;

    /**
     * {@inheritdoc}
     */
    protected function setup(): void
    {
        $this->prophet = new \Prophecy\Prophet();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    /**
     * test constructor
     */
    public function testConstruct()
    {
        $helper = $this->prophet->prophesize(QuestionHelper::class);
        $input = $this->prophet->prophesize(InputInterface::class);
        $output = $this->prophet->prophesize(OutputInterface::class);
        $operator = new ConsoleOperator($helper->reveal(), $input->reveal(), $output->reveal());

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
