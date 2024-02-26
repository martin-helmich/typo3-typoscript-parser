<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer;

class Token implements TokenInterface
{
    private string $type;

    private string $value;

    private int $line;

    private int $column;

    private array $patternMatches;

    public function __construct(string $type, string $value, int $line, int $column = 1, array $patternMatches = [])
    {
        $this->type           = $type;
        $this->value          = $value;
        $this->line           = $line;
        $this->column         = $column;
        $this->patternMatches = $patternMatches;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSubMatch(string $name): ?string
    {
        return isset($this->patternMatches[$name]) ? $this->patternMatches[$name] : null;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}
