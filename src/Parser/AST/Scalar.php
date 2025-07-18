<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * A scalar value.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class Scalar implements Node
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string[]
     */
    public function getSubNodeNames(): array
    {
        return ['value'];
    }
}
