<?php
namespace Helmich\TypoScriptParser\Tokenizer;

/**
 * Helper class for building tokens that span multiple lines.
 *
 * Examples are multi-line comments or "("-assignments.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer
 */
class MultilineTokenBuilder
{
    /** @var string */
    private $type = null;

    /** @var string */
    private $value = null;

    /** @var int */
    private $startLine = null;

    /** @var int */
    private $startColumn = null;

    /**
     * @param string $type   Token type, one of `TokenInterface::TYPE_*`
     * @param string $value  Token value
     * @param int    $line   Starting line in source code
     * @param int    $column Starting column in source code
     */
    public function startMultilineToken($type, $value, $line, $column)
    {
        $this->type        = $type;
        $this->value       = $value;
        $this->startLine   = $line;
        $this->startColumn = $column;
    }

    /**
     * @param string $append Token content to append
     */
    public function appendToToken($append)
    {
        $this->value .= $append;
    }

    /**
     * @param string $append Token content to append
     * @return TokenInterface
     */
    public function endMultilineToken($append = '')
    {
        $this->value .= $append;

        $token = new Token(
            $this->type,
            rtrim($this->value),
            $this->startLine,
            $this->startColumn
        );

        $this->reset();
        return $token;
    }

    /**
     * @return string Token type (one of `TokenInterface::TYPE_*`)
     */
    public function currentTokenType()
    {
        return $this->type;
    }

    /**
     * @return void
     */
    private function reset()
    {
        $this->type        = null;
        $this->value       = null;
        $this->startLine   = null;
        $this->startColumn = null;
    }
}
