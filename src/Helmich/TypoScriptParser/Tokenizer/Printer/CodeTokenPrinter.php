<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer\Printer;

use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

class CodeTokenPrinter implements TokenPrinterInterface
{
    /**
     * @param TokenInterface[] $tokens
     * @return string
     */
    public function printTokenStream(array $tokens): string
    {
        $content = '';

        foreach ($tokens as $token) {
            $content .= $token->getValue();
        }

        return $content;
    }
}
