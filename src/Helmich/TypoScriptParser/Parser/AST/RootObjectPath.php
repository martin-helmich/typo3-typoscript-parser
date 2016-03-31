<?php
namespace Helmich\TypoScriptParser\Parser\AST;

class RootObjectPath extends ObjectPath
{
    public function __construct()
    {
        parent::__construct('', '');
    }

    public function parent()
    {
        return $this;
    }

    public function append($name)
    {
        return new ObjectPath(ltrim($name, '.'), $name);
    }
}