<?php
namespace Helmich\TypoScriptParser\Tokenizer\Printer;

use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

interface TokenPrinterInterface
{
    /**
     * @param TokenInterface[] $tokens
     * @return string
     */
    public function printTokenStream(array $tokens);
} 
