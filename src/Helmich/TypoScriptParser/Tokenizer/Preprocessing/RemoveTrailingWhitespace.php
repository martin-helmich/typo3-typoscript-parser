<?php
namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Preprocessor that removes trailing whitespaces from a file
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class RemoveTrailingWhitespace implements Preprocessor
{
    /** @var string */
    private $eolCharacter;

    public function __construct($eolCharacter = "\n")
    {
        $this->eolCharacter = $eolCharacter;
    }

    /**
     * @param string $contents Un-processed Typoscript contents
     * @return string Processed TypoScript contents
     */
    public function preprocess($contents)
    {
        // Remove trailing whitespaces.
        $lines   = explode($this->eolCharacter, $contents);
        $lines   = array_map('rtrim', $lines);
        $content = implode($this->eolCharacter, $lines);

        return $content;
    }
}