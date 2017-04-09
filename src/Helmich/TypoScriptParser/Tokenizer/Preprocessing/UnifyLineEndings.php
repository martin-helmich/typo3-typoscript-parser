<?php
namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Preprocessor that unifies line endings for a file
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class UnifyLineEndings implements Preprocessor
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
        return preg_replace(",(\r\n|\r|\n),", $this->eolCharacter, $contents);
    }
}