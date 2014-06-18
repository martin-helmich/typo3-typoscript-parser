<?php
namespace Helmich\TypoScriptParser\Parser\AST\Operator;


use Helmich\TypoScriptParser\Parser\AST\ObjectPath;


/**
 * A copy assignment.
 *
 * Example:
 *
 *     foo = bar
 *     baz < foo
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
class Copy extends BinaryOperator
{



    /**
     * The object path to copy the value from.
     * @var \Helmich\TypoScriptParser\Parser\AST\ObjectPath
     */
    public $target;



    /**
     * Constructs a copy statement.
     *
     * @param \Helmich\TypoScriptParser\Parser\AST\ObjectPath $object     The object to copy the value to.
     * @param \Helmich\TypoScriptParser\Parser\AST\ObjectPath $target     The object to copy the value from.
     * @param int                                             $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, ObjectPath $target, $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
        $this->target = $target;
    }



}