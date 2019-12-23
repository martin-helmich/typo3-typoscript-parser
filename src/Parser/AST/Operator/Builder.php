<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST\Operator;

/**
 * Helper class for quickly building operator AST nodes
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 *
 * @method ObjectCreation objectCreation($path, $value, $line)
 * @method Assignment assignment($path, $value, $line)
 * @method Copy copy($path, $value, $line)
 * @method Reference reference($path, $value, $line)
 * @method Delete delete($path, $line)
 * @method ModificationCall modificationCall($method, $arguments)
 * @method Modification modification($path, $call, $line)
 */
class Builder
{
    public function __call($name, $args)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name);
        return (new \ReflectionClass($class))->newInstanceArgs($args);

        // Curse you, and fucking die already, PHP 5.5
        //return new $class(...$args);
    }
}
