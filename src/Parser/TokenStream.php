<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser;

use ArrayAccess;
use BadMethodCallException;
use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Iterator;

/**
 * Helper class that represents a token stream
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser
 *
 * @template-implements ArrayAccess<int, TokenInterface>
 * @template-implements Iterator<int, TokenInterface>
 */
class TokenStream implements Iterator, ArrayAccess
{
    /**
     * @var TokenInterface[]
     */
    private array $tokens;

    private int $index = 0;

    /**
     * @param TokenInterface[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function current(int $lookAhead = 0): TokenInterface
    {
        return $this[$this->index + $lookAhead];
    }

    public function next(int $increment = 1): void
    {
        if ($this->index < count($this->tokens)) {
            $this->index += $increment;
        }
    }

    public function valid(): bool
    {
        return ($this->index) < count($this->tokens);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function key(): int
    {
        return $this->index;
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $offset >= 0 && $offset < count($this->tokens);
    }

    /**
     * @param int $offset
     * @return TokenInterface
     */
    public function offsetGet($offset): TokenInterface
    {
        return $this->tokens[$offset];
    }

    /**
     * @param int $offset
     * @param TokenInterface|null $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('changing a token stream is not permitted');
    }

    /**
     * @param int $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('changing a token stream is not permitted');
    }

    /**
     * Normalizes the token stream.
     *
     * This method transforms the token stream in a normalized form. This
     * includes:
     *
     *   - trimming whitespaces (remove leading and trailing whitespaces, as
     *     those are irrelevant for the parser)
     *   - remove both one-line and multi-line comments (also irrelevant for the
     *     parser)
     *
     * @return TokenStream
     */
    public function normalized(): TokenStream
    {
        $filteredTokens = [];

        $maxLine = 0;

        foreach ($this->tokens as $token) {
            $maxLine = max($token->getLine(), $maxLine);

            // Trim unnecessary whitespace, but leave line breaks! These are important!
            if ($token->getType() === TokenInterface::TYPE_WHITESPACE) {
                $value = trim($token->getValue(), "\t ");
                if (strlen($value) > 0) {
                    $filteredTokens[] = new Token(
                        TokenInterface::TYPE_WHITESPACE,
                        $value,
                        $token->getLine(),
                        $token->getColumn()
                    );
                }
            } else {
                $filteredTokens[] = $token;
            }
        }

        // Add two linebreak tokens; during parsing, we usually do not look more than two
        // tokens ahead; this hack ensures that there will always be at least two more tokens
        // present and we do not have to check whether these tokens exists.
        $filteredTokens[] = new Token(TokenInterface::TYPE_WHITESPACE, "\n", $maxLine + 1, 1);
        $filteredTokens[] = new Token(TokenInterface::TYPE_WHITESPACE, "\n", $maxLine + 2, 1);

        return new TokenStream($filteredTokens);
    }
}
