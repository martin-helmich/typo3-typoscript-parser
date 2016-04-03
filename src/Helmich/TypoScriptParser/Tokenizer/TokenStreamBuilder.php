<?php
namespace Helmich\TypoScriptParser\Tokenizer;

use ArrayObject;

class TokenStreamBuilder
{
    /** @var ArrayObject */
    private $tokens;

    public function __construct()
    {
        $this->tokens = new ArrayObject();
    }

    public function append($type, $value, $line, array $patternMatches = [])
    {
        $this->tokens->append(new Token($type, $value, $line, $patternMatches));
    }

    public function count()
    {
        return $this->tokens->count();
    }

    /**
     * @return ArrayObject
     */
    public function build()
    {
        return $this->tokens;
    }
}