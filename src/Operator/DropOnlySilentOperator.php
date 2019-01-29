<?php

namespace Howyi\Conv\Operator;

class DropOnlySilentOperator implements OperatorInterface
{
    /**
     * @param string $message
     * @param array  $choices
     * @return mixed
     */
    public function choiceQuestion(string $message, array $choices)
    {
        if (in_array('dropped', $choices, true)) {
            return 'dropped';
        }
        throw new \RuntimeException('"dropped" answer not exist.');
    }

    /**
     * @param string $message
     */
    public function output(string $message): void
    {
        return;
    }

    /**
     * @param int $max
     */
    public function startProgress(int $max): void
    {
        return;
    }

    /**
     * @param int $step
     */
    public function advanceProgress(int $step = 1): void
    {
        return;
    }

    public function setProgressFormat(string $format): void
    {
        return;
    }

    public function finishProgress(string $message): void
    {
        return;
    }
}
