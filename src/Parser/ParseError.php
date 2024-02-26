<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser;

use Exception;

class ParseError extends \Exception
{

    private ?int $sourceLine;

    public function __construct(string $message = "", int $code = 0, ?int $line = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->sourceLine = $line;
    }

    public function getSourceLine(): ?int
    {
        return $this->sourceLine;
    }
}
