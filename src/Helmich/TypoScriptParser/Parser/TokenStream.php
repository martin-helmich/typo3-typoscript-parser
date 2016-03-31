<?php
namespace Helmich\TypoScriptParser\Parser;

use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Iterator;

class TokenStream implements Iterator
{
    /** @var array */
    private $tokens;

    /** @var int */
    private $index = 0;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param int $lookAhead
     * @return TokenInterface
     */
    public function current($lookAhead = 0)
    {
        return $this->tokens[$this->index + $lookAhead];
    }

    /**
     * @param int $increment
     * @return void
     */
    public function next($increment = 1)
    {
        if ($this->index < count($this->tokens)) {
            $this->index += $increment;
        }
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return ($this->index + 1) < count($this->tokens);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

}