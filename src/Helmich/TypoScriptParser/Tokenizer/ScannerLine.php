<?php
namespace Helmich\TypoScriptParser\Tokenizer;

class ScannerLine
{
    private $line;
    private $index;
    private $original;

    public function __construct($index, $line)
    {
        $this->line     = $line;
        $this->original = $line;
        $this->index    = $index;
    }

    /**
     * @param string $pattern
     * @return array
     */
    public function scan($pattern)
    {
        if (preg_match($pattern, $this->line, $matches)) {
            $this->line = substr($this->line, strlen($matches[0]));
            return $matches;
        }

        return false;
    }

    /**
     * @param string $pattern
     * @return array
     */
    public function peek($pattern)
    {
        if (preg_match($pattern, $this->line, $matches)) {
            return $matches;
        }

        return false;
    }

    public function index()
    {
        return $this->index;
    }

    public function value()
    {
        return $this->line;
    }

    public function __toString()
    {
        return $this->original;
    }
}
