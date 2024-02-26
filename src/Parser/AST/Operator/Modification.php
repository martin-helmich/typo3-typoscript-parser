<?php declare(strict_types=1);

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
    public ModificationCall $call;

    /**
     * Constructs a modification statement.
     *
     * @param ObjectPath       $object     The object to modify.
     * @param ModificationCall $call       The modification call.
     * @param int              $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, ModificationCall $call, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object = $object;
        $this->call   = $call;
    }
}
