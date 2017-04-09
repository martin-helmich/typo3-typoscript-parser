<?php
namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Helper class that provides the standard pre-processing behaviour
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class Standard extends ProcessorChain
{
    public function __construct($eolChar = "\n")
    {
        $this->processors = [
            new UnifyLineEndings($eolChar),
            new RemoveTrailingWhitespace($eolChar)
        ];
    }
}
