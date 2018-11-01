<?php

namespace Laminaria\Conv\Operator;

interface OperatorInterface
{
    /**
     * @param string $message
     * @param array  $choices
     * @return mixed
     */
    public function choiceQuestion(string $message, array $choices);

    /**
     * @param string $message
     */
    public function output(string $message): void;

    /**
     * @param int $max
     */
    public function startProgress(int $max): void;

    /**
     * @param int $step
     */
    public function advanceProgress(int $step = 1): void;

    /**
     * @param string $format
     */
    public function setProgressFormat(string $format): void;

    /**
     * @param string $message
     */
    public function finishProgress(string $message): void;
}
