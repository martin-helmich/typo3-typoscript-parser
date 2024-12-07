<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST\Operator;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use Helmich\TypoScriptParser\Parser\AST\Statement;

/**
 * Helper class for quickly building operator AST nodes
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 *
 * @method ObjectCreation objectCreation(ObjectPath $path, Scalar $value, int $line)
 * @method Assignment assignment(ObjectPath $path, Scalar $value, int $line)
 * @method Copy copy(ObjectPath $path, ObjectPath $value, int $line)
 * @method Reference reference(ObjectPath $path, ObjectPath $value, int $line)
 * @method Delete delete(ObjectPath $path, int $line)
 * @method ModificationCall modificationCall(string $method, string $arguments)
 * @method Modification modification(ObjectPath $path, ModificationCall $call, int $line)
 */
class Builder
{
    /**
     * @param string $name
     * @param mixed[] $args
     * @return object
     */
    public function __call(string $name, array $args): object
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name);
        return new $class(...$args);
    }
}
