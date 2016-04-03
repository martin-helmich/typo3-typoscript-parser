<?php
namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * Class RootObjectPath
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class RootObjectPath extends ObjectPath
{
    /**
     * RootObjectPath constructor.
     */
    public function __construct()
    {
        parent::__construct('', '');
    }

    /**
     * @return ObjectPath
     */
    public function parent()
    {
        return $this;
    }

    /**
     * @return int
     */
    public function depth()
    {
        return 0;
    }

    /**
     * @param string $name
     * @return ObjectPath
     */
    public function append($name)
    {
        return new ObjectPath(ltrim($name, '.'), $name);
    }
}
