<?php declare(strict_types=1);

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
class Copy extends BinaryObjectOperator
{
    /**
     * Constructs a copy statement.
     *
     * @param ObjectPath $object     The object to copy the value to.
     * @param ObjectPath $target     The object to copy the value from.
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
