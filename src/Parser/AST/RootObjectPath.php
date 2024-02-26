<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * Class RootObjectPath
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class RootObjectPath extends ObjectPath
{
    public function __construct()
    {
        parent::__construct('', '');
    }

    public function parent(): ObjectPath
    {
        return $this;
    }

    public function depth(): int
    {
        return 0;
    }

    public function append(string $name): ObjectPath
    {
        return new ObjectPath(ltrim($name, '.'), $name);
    }
}
