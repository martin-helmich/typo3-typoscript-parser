<?php
namespace Helmich\TypoScriptParser\Parser\AST\Operator;


use Helmich\TypoScriptParser\Parser\AST\Statement;


/**
 * Abstract base class for statements with binary operators.
 *
 * @package    Helmich\TypoScriptParser
 * @subpcakage Parser\AST\Operator
 */
abstract class BinaryOperator extends Statement
{



    /**
     * The object on the left-hand side of the statement.
     *
     * @var \Helmich\TypoScriptParser\Parser\AST\ObjectPath
     */
    public $object;


}