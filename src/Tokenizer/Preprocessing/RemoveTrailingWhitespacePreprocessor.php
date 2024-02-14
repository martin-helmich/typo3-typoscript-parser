<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Preprocessor that removes trailing whitespaces from a file
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class RemoveTrailingWhitespacePreprocessor implements Preprocessor
{
    /** @psalm-var non-empty-string */
    private string $eolCharacter;

    /**
     * @psalm-param non-empty-string $eolCharacter
     */
    public function __construct(string $eolCharacter = "\n")
    {
        $this->eolCharacter = $eolCharacter;
    }

    /**
     * @param string $contents Un-processed Typoscript contents
     * @return string Processed TypoScript contents
     */
    public function preprocess(string $contents): string
    {
        // Remove trailing whitespaces.
        $lines   = explode($this->eolCharacter, $contents);
        $lines   = array_map('rtrim', $lines);
        $content = implode($this->eolCharacter, $lines);

        return $content;
    }
}
