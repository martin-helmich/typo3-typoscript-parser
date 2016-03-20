<?php
namespace Helmich\TypoScriptParser\Tokenizer;

interface TokenizerInterface
{

    /**
     * @param string $inputString
     * @return \Helmich\TypoScriptParser\Tokenizer\TokenInterface[]
     */
    public function tokenizeString($inputString);

    /**
     * @param string $inputStream
     * @return \Helmich\TypoScriptParser\Tokenizer\TokenInterface[]
     */
    public function tokenizeStream($inputStream);

}