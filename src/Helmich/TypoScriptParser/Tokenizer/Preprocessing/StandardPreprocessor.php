<?php
namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Helper class that provides the standard pre-processing behaviour
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class StandardPreprocessor extends ProcessorChain
{
    public function __construct($eolChar = "\n")
    {
        $this->processors = [
            new UnifyLineEndingsPreprocessor($eolChar),
            new RemoveTrailingWhitespacePreprocessor($eolChar)
        ];
    }
}
