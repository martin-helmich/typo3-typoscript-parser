<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * A nested assignment statement.
 *
 * Example:
 *
 *     foo {
 *         bar = 1
 *         baz = 2
 *     }
 *
 * Which is equivalent to
 *
 *     foo.bar = 1
 *     foo.baz = 2
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class NestedAssignment extends Statement
{

    /**
     * The object to operate on.
     */
    public ObjectPath $object;

    /**
     * The nested statements.
     *
     * @var Statement[]
     */
    public array $statements;

    /**
     * @param ObjectPath  $object     The object to operate on.
     * @param Statement[] $statements The nested statements.
     * @param int         $sourceLine The original source line.
     */
    public function __construct(ObjectPath $object, array $statements, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->object     = $object;
        $this->statements = $statements;
    }
}
