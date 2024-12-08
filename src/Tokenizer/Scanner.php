<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer;

use Iterator;

/**
 * Helper class for scanning lines
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer
 *
 * @template-implements Iterator<int, ScannerLine>
 */
class Scanner implements Iterator
{
    /** @var string[] */
    private array $lines;

    private int $index = 0;

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function current(): ScannerLine
    {
        return new ScannerLine($this->index + 1, $this->lines[$this->index]);
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return $this->index < count($this->lines);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
