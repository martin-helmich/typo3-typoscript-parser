<?php
namespace Helmich\TypoScriptParser\Parser\AST\Operator;


use Helmich\TypoScriptParser\Parser\AST\ObjectPath;


/**
 * A modification statement.
 *
 * Example:
 *
 *     foo  = bar
 *     foo := appendToString(baz)
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
class Modification extends BinaryOperator
{



    /**
     * The modification call.
     * @var \Helmich\TypoScriptParser\Parser\AST\Operator\ModificationCall
     */
    public $call;



    /**
     * Constructs a modification statement.
     *
     * @param \Helmich\TypoScriptParser\Parser\AST\ObjectPath                $object     The object to modify.
     * @param \Helmich\TypoScriptParser\Parser\AST\Operator\ModificationCall $call       The modification call.
     * @param int                                                            $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, ModificationCall $call, $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
        $this->call   = $call;
    }

}