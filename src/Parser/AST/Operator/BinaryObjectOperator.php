<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST\Operator;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;

/**
 * Abstract base class for statements with binary operators.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
abstract class BinaryObjectOperator extends BinaryOperator
{
    /**
     * The target object to reference to or copy from.
     */
    public ObjectPath $target;

    public function getSubNodeNames(): array
    {
        return ['target'];
    }
}
