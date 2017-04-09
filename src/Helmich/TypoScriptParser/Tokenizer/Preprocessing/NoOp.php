<?php
namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Preprocessor that does not actually do anything
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class NoOp implements Preprocessor
{
    /**
     * @param string $contents Un-processed Typoscript contents
     * @return string Processed TypoScript contents
     */
    public function preprocess($contents)
    {
        return $contents;
    }
}
