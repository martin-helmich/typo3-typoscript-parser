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
     * @param bool   $peek
     * @return array
     */
    public function scan($pattern, $peek = false)
    {
        if (preg_match($pattern, $this->line, $matches)) {
            if (!$peek) {
                $this->line = substr($this->line, strlen($matches[0]));
            }
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
        return $this->scan($pattern, true);
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
