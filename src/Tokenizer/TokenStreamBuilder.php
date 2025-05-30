<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer;

use ArrayObject;

/**
 * Helper class for building a token stream
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer
 */
class TokenStreamBuilder
{
    /**
     * @var ArrayObject<int, TokenInterface>
     */
    private ArrayObject $tokens;

    private ?int $currentLine = null;

    private int $currentColumn = 1;

    /**
     * TokenStreamBuilder constructor.
     */
    public function __construct()
    {
        $this->tokens = new ArrayObject();
    }

    /**
     * Appends a new token to the token stream
     *
     * @param string   $type           Token type
     * @param string   $value          Token value
     * @param int      $line           Line in source code
     * @param string[] $patternMatches Subpattern matches
     * @return void
     */
    public function append(string $type, string $value, int $line, array $patternMatches = []): void
    {
        if ($this->currentLine !== $line) {
            $this->currentLine   = $line;
            $this->currentColumn = 1;
        }

        $this->tokens->append(new Token($type, $value, $line, $this->currentColumn, $patternMatches));

        $this->currentColumn += strlen($value);
    }

    /**
     * Appends a new token to the token stream
     *
     * @param TokenInterface $token The token to append
     * @return void
     */
    public function appendToken(TokenInterface $token): void
    {
        $this->tokens->append($token);
    }

    /**
     * @return int The length of the token stream
     */
    public function count(): int
    {
        return $this->tokens->count();
    }

    public function currentColumn(): int
    {
        return $this->currentColumn;
    }

    /**
     * @return ArrayObject<int, TokenInterface> The completed token stream
     */
    public function build(): ArrayObject
    {
        return $this->tokens;
    }
}
