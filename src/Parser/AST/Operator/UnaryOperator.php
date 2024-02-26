<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST\Operator;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Statement;

/**
 * Abstract base class for statements with unary operators.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
abstract class UnaryOperator extends Statement
{
    /**
     * The object the operator should be applied on.
     */
    public ObjectPath $object;

    /**
     * Constructs a unary operator statement.
     *
     * @param ObjectPath $object     The object to operate on.
     * @param int        $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
    }
}
