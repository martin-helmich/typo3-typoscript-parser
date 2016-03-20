<?php
namespace Helmich\TypoScriptParser\Parser\AST\Operator;


use Helmich\TypoScriptParser\Parser\AST\ObjectPath;


/**
 * A reference statement.
 *
 * Example:
 *
 *     foo = bar
 *     baz <= foo
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
class Reference extends BinaryObjectOperator
{

    /**
     * Constructs a new reference statement.
     *
     * @param \Helmich\TypoScriptParser\Parser\AST\ObjectPath $object     The reference object.
     * @param \Helmich\TypoScriptParser\Parser\AST\ObjectPath $target     The target object.
     * @param int                                             $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, ObjectPath $target, $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
        $this->target = $target;
    }
}