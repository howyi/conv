<?php

namespace Howyi\Conv\Operator;

class DropOnlyConsoleOperator extends ConsoleOperator
{
    /**
     * @param string $message
     * @param array  $choices
     * @return mixed|string
     */
    public function choiceQuestion(string $message, array $choices)
    {
        $this->output($message);
        if (in_array('dropped', $choices, true)) {
            $this->output('<fg=red>-> dropped</>');
            return 'dropped';
        }
        throw new \RuntimeException('"dropped" answer not exist.');
    }
}
