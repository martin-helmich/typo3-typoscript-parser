<?php declare(strict_types=1);

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
     * @param ObjectPath $object     The reference object.
     * @param ObjectPath $target     The target object.
     * @param int        $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, ObjectPath $target, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
        $this->target = $target;
    }

    public function getSubNodeNames(): array
    {
        return ['object', 'target'];
    }
}
