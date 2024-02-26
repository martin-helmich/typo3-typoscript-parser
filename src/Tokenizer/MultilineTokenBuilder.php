<?php declare(strict_types=1);

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
    private ?string $type = null;

    private ?string $value = null;

    private ?int $startLine = null;

    private ?int $startColumn = null;

    /**
     * @param string $type   Token type, one of `TokenInterface::TYPE_*`
     * @param string $value  Token value
     * @param int    $line   Starting line in source code
     * @param int    $column Starting column in source code
     */
    public function startMultilineToken(string $type, string $value, int $line, int $column): void
    {
        $this->type        = $type;
        $this->value       = $value;
        $this->startLine   = $line;
        $this->startColumn = $column;
    }

    /**
     * @param string $append Token content to append
     */
    public function appendToToken(string $append): void
    {
        if ($this->value === null) {
            $this->value = "";
        }

        $this->value .= $append;
    }

    /**
     * @param string $append Token content to append
     * @return TokenInterface
     */
    public function endMultilineToken(string $append = ''): TokenInterface
    {
        $value       = ($this->value ?? "") . $append;
        $type        = $this->type;
        $startLine   = $this->startLine;
        $startColumn = $this->startColumn;

        if ($type === null || $startLine === null || $startColumn === null) {
            throw new TokenizerException('cannot call "endMultilineToken" before calling "startMultilineToken"');
        }

        $token = new Token(
            $type,
            rtrim($value),
            $startLine,
            $startColumn
        );

        $this->reset();
        return $token;
    }

    /**
     * @return string|null Token type (one of `TokenInterface::TYPE_*`)
     */
    public function currentTokenType(): ?string
    {
        return $this->type;
    }

    /**
     * @return void
     */
    private function reset(): void
    {
        $this->type        = null;
        $this->value       = null;
        $this->startLine   = null;
        $this->startColumn = null;
    }
}
