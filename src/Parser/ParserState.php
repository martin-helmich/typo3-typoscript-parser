<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser;

use ArrayObject;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\RootObjectPath;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

class ParserState
{
    private ObjectPath $context;

    private ArrayObject $statements;

    private TokenStream $tokens;

    public function __construct(TokenStream $tokens, ArrayObject $statements = null)
    {
        if ($statements === null) {
            $statements = new ArrayObject();
        }

        $this->statements = $statements;
        $this->tokens     = $tokens;
        $this->context    = new RootObjectPath();
    }

    public function withContext(ObjectPath $context): self
    {
        $clone          = clone $this;
        $clone->context = $context;
        return $clone;
    }

    public function withStatements(ArrayObject $statements): self
    {
        $clone             = clone $this;
        $clone->statements = $statements;
        return $clone;
    }

    public function token(int $lookAhead = 0): TokenInterface
    {
        return $this->tokens->current($lookAhead);
    }

    public function next(int $increment = 1): void
    {
        $this->tokens->next($increment);
    }

    public function hasNext(): bool
    {
        return $this->tokens->valid();
    }

    public function context(): ObjectPath
    {
        return $this->context;
    }

    public function statements(): ArrayObject
    {
        return $this->statements;
    }
}
