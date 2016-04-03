<?php
namespace Helmich\TypoScriptParser\Tokenizer;

/**
 * Interface TokenizerInterface
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer
 */
interface TokenizerInterface
{

    /**
     * @param string $inputString
     * @return TokenInterface[]
     */
    public function tokenizeString($inputString);

    /**
     * @param string $inputStream
     * @return TokenInterface[]
     */
    public function tokenizeStream($inputStream);

}