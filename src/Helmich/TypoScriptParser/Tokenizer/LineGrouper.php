<?php
namespace Helmich\TypoScriptParser\Tokenizer;

/**
 * Class LineGrouper
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Tokenizer
 */
class LineGrouper
{
    private $tokensByLine = [];

    /**
     * @param TokenInterface[] $tokens
     */
    public function __construct(array $tokens)
    {
        foreach ($tokens as $token) {
            if (!array_key_exists($token->getLine(), $this->tokensByLine)) {
                $this->tokensByLine[$token->getLine()] = [];
            }
            $this->tokensByLine[$token->getLine()][] = $token;
        }
    }

    /**
     * @return TokenInterface[][]
     */
    public function getLines()
    {
        return $this->tokensByLine;
    }
}
