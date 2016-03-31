<?php
namespace Helmich\TypoScriptParser\Parser;

class IteratorState
{
    private $val = 0;

    public function next($increment = 1)
    {
        $this->val += $increment;
    }

    public function value()
    {
        return $this->val;
    }
}