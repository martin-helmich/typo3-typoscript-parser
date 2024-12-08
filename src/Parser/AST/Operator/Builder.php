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
 * @method Modification modification(ObjectPath $path, ModificationCall $call, int $line)
 */
class Builder
{

    /**
     * @param string $name
     * @param mixed[] $args
     * @return Statement
     */
    public function __call(string $name, array $args): Statement
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name);
        $classInstance = new $class(...$args);

        assert($classInstance instanceof Statement);
        return $classInstance;
    }

    public function modificationCall(string $method, string $arguments): ModificationCall
    {
        // Needs a special implementation, because ModificationCall is not a Statement
        return new ModificationCall($method, $arguments);
    }
}
