<?php
namespace Helmich\TypoScriptParser\Tokenizer;

class Scanner
{
    public function scan(&$line, $pattern, $peek = false)
    {
        if (preg_match($pattern, $line, $matches)) {
            if (!$peek) {
                $line = substr($line, strlen($matches[0]));
            }
            return $matches;
        }

        return false;
    }

    public function peek($line, $pattern)
    {
        return $this->scan($line, $pattern, true);
    }
}