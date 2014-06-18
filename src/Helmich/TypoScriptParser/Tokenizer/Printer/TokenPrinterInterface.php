<?php
namespace Helmich\TypoScriptParser\Tokenizer\Printer;


interface TokenPrinterInterface
{



    /**
     * @param \Helmich\TypoScriptParser\Tokenizer\TokenInterface[] $tokens
     * @return string
     */
    public function printTokenStream(array $tokens);

} 