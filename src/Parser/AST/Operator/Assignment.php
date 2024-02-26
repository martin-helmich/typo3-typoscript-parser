<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST\Operator;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

/**
 * An assignment statement.
 *
 * Example:
 *
 *     foo = bar
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
class Assignment extends BinaryOperator
{
    /**
     * The value to be assigned. Should be a scalar value, which MAY contain
     * a constant evaluation expression (like "${foo.bar}").
     */
    public Scalar $value;

    /**
     * Constructs an assignment.
     *
     * @param ObjectPath $object     The object to which to assign the value.
     * @param Scalar     $value      The value to be assigned.
     * @param int        $sourceLine The source line.
     */
    public function __construct(ObjectPath $object, Scalar $value, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
        $this->value  = $value;
    }
}
