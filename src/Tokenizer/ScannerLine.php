<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer;

class ScannerLine
{
    private string $line;
    private int $index;
    private string $original;

    public function __construct(int $index, string $line)
    {
        $this->line     = $line;
        $this->original = $line;
        $this->index    = $index;
    }

    /**
     * @param string $pattern
     * @psalm-param non-empty-string $pattern
     * @return string[]|false
     */
    public function scan(string $pattern): array|false
    {
        if (preg_match($pattern, $this->line, $matches)) {
            $matchingPart = substr($this->line, strlen($matches[0]));
            $this->line = $matchingPart !== false ? $matchingPart : '';
            return $matches;
        }

        return false;
    }

    /**
     * @param string $pattern
     * @psalm-param non-empty-string $pattern
     * @return string[]|false
     */
    public function peek(string $pattern): array|false
    {
        if (preg_match($pattern, $this->line, $matches)) {
            return $matches;
        }

        return false;
    }

    public function index(): int
    {
        return $this->index;
    }

    public function value(): string
    {
        return $this->line;
    }

    public function length(): int
    {
        return strlen($this->line);
    }

    public function __toString(): string
    {
        return $this->original;
    }
}
