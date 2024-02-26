<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Helper class that provides the standard pre-processing behaviour
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class StandardPreprocessor extends ProcessorChain
{
    /**
     * @psalm-param non-empty-string $eolChar
     */
    public function __construct(string $eolChar = "\n")
    {
        $this->processors = [
            new UnifyLineEndingsPreprocessor($eolChar),
            new RemoveTrailingWhitespacePreprocessor($eolChar),
        ];
    }
}
