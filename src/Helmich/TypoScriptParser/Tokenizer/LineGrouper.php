<?php
namespace Helmich\TypoScriptParser\Tokenizer;


class LineGrouper
{



    private $tokensByLine = [];



    /**
     * @param \Helmich\TypoScriptParser\Tokenizer\TokenInterface[] $tokens
     */
    public function __construct(array $tokens)
    {
        foreach ($tokens as $token)
        {
            if (!array_key_exists($token->getLine(), $this->tokensByLine))
            {
                $this->tokensByLine[$token->getLine()] = [];
            }
            $this->tokensByLine[$token->getLine()][] = $token;
        }
    }



    public function getLines()
    {
        return $this->tokensByLine;
    }

}