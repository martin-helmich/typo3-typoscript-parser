<?php
namespace Helmich\TypoScriptParser\Parser\AST\Operator;

/**
 * Abstract base class for statements with binary operators.
 *
 * @package    Helmich\TypoScriptParser
 * @subpcakage Parser\AST\Operator
 */
abstract class BinaryObjectOperator extends BinaryOperator
{
    /**
     * The target object to reference to or copy from.
     * @var \Helmich\TypoScriptParser\Parser\AST\ObjectPath
     */
    public $target;
}