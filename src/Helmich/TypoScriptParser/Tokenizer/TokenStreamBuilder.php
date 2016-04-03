<?php
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
    /** @var ArrayObject */
    private $tokens;

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
     * @param string $type           Token type
     * @param string $value          Token value
     * @param int    $line           Line in source code
     * @param array  $patternMatches Subpattern matches
     * @return void
     */
    public function append($type, $value, $line, array $patternMatches = [])
    {
        $this->tokens->append(new Token($type, $value, $line, $patternMatches));
    }

    /**
     * Appends a new token to the token stream
     *
     * @param TokenInterface $token The token to append
     * @return void
     */
    public function appendToken(TokenInterface $token)
    {
        $this->tokens->append($token);
    }

    /**
     * @return int The length of the token stream
     */
    public function count()
    {
        return $this->tokens->count();
    }

    /**
     * @return ArrayObject The completed token stream
     */
    public function build()
    {
        return $this->tokens;
    }
}