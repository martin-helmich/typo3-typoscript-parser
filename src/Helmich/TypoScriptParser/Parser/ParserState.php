<?php
namespace Helmich\TypoScriptParser\Parser;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\RootObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

class ParserState
{
    /** @var ObjectPath */
    private $context;

    /** @var IteratorState */
    private $index;

    /** @var \ArrayObject */
    private $statements = null;

    /** @var TokenInterface[] */
    private $tokens = [];

    public function __construct(\ArrayObject $statements = null, array $tokens = [])
    {
        if ($statements === null) {
            $statements = new \ArrayObject();
        }

        $this->statements = $statements;
        $this->tokens     = $tokens;
        $this->index      = new IteratorState();
        $this->context    = new RootObjectPath();
    }

    public function withContext(ObjectPath $context)
    {
        $clone = clone $this;
        $clone->context = $context;
        return $clone;
    }

    public function withoutContext()
    {
        $clone = clone $this;
        $clone->context = null;
        return $clone;
    }

    public function withStatements(\ArrayObject $statements)
    {
        $clone = clone $this;
        $clone->statements = $statements;
        return $clone;
    }

    /**
     * @return TokenInterface[]
     */
    public function tokens()
    {
        return $this->tokens;
    }

    /**
     * @param int $lookAhead
     * @return TokenInterface
     */
    public function token($lookAhead = 0)
    {
        return $this->tokens[$this->index->value() + $lookAhead];
    }

    /**
     * @param int $increment
     * @return bool
     */
    public function next($increment = 1)
    {
        if ($this->index->value() < count($this->tokens)) {
            $this->index->next($increment);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return ($this->index->value() + 1) < count($this->tokens);
    }

    /**
     * @return ObjectPath
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * @return \ArrayObject
     */
    public function statements()
    {
        return $this->statements;
    }

    /**
     * @return int
     */
    public function index()
    {
        return $this->index->value();
    }
}