<?php
namespace Helmich\TypoScriptParser\Parser;

use BadMethodCallException;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Iterator;

/**
 * Helper class that represents a token stream
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser
 */
class TokenStream implements Iterator, \ArrayAccess
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
        return $this[$this->index + $lookAhead];
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
        return ($this->index) < count($this->tokens);
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

    /**
     * @param int $offset
     * @return TokenInterface
     */
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < count($this->tokens);
    }

    /**
     * @param int $offset
     * @return TokenInterface
     */
    public function offsetGet($offset)
    {
        return $this->tokens[$offset];
    }

    /**
     * @param int            $offset
     * @param TokenInterface $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('changing a token stream is not permitted');
    }

    /**
     * @param int $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('changing a token stream is not permitted');
    }
}