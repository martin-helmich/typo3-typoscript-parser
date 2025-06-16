<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

interface Node
{
    /**
     * Gets the names of the sub nodes.
     *
     * @return string[] Names of sub nodes
     */
    public function getSubNodeNames(): array;
}
