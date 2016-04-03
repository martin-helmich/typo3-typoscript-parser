<?php
namespace Helmich\TypoScriptParser\Tokenizer\Printer;

use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

/**
 * Interface definition for a class that prints token streams
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer\Printer
 */
interface TokenPrinterInterface
{
    /**
     * @param TokenInterface[] $tokens
     * @return string
     */
    public function printTokenStream(array $tokens);
}
