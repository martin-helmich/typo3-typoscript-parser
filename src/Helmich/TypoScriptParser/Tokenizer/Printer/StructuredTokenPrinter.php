<?php
namespace Helmich\TypoScriptParser\Tokenizer\Printer;

use Symfony\Component\Yaml\Yaml;

class StructuredTokenPrinter implements TokenPrinterInterface
{

    /**
     * @param \Helmich\TypoScriptParser\Tokenizer\TokenInterface[] $tokens
     * @return string
     */
    public function printTokenStream(array $tokens)
    {
        $content = '';

        foreach ($tokens as $token) {
            $content .= sprintf("%20s %s\n", $token->getType(), Yaml::dump($token->getValue()));
        }

        return $content;
    }
}