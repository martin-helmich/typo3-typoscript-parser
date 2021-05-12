<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * A scalar value.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class ScalarValue
{
    /**
     * The value.
     *
     * @var string
     */
    public $value;

    /**
     * Constructs a scalar value.
     *
     * @param string $value The value.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
