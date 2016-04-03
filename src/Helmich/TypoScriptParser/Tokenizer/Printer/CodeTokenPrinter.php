<?php
namespace Helmich\TypoScriptParser\Tokenizer\Printer;

class CodeTokenPrinter implements TokenPrinterInterface
{
    /**
     * @param \Helmich\TypoScriptParser\Tokenizer\TokenInterface[] $tokens
     * @return string
     */
    public function printTokenStream(array $tokens)
    {
        $content = '';

        foreach ($tokens as $token) {
            #$content .= sprintf("%20s %s\n", $token->getType(), Yaml::dump($token->getValue()));
            $content .= $token->getValue();
        }

        return $content;
    }
}
