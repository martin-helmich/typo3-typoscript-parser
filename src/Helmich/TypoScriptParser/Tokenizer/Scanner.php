<?php
namespace Helmich\TypoScriptParser\Tokenizer;

/**
 * Helper class for scanning lines
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer
 */
class Scanner implements \Iterator
{
    /** @var array */
    private $lines = [];
    private $index = 0;

    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function current()
    {
        return new ScannerLine($this->index + 1, $this->lines[$this->index]);
    }

    public function next()
    {
        $this->index ++;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return $this->index < count($this->lines);
    }

    public function rewind()
    {
        $this->index = 0;
    }
}
