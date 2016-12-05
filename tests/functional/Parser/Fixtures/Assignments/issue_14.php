<?php
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new NestedAssignment(
        new ObjectPath('page', 'page'), [
            new NestedAssignment(
                new ObjectPath('page.meta', 'meta'), [
                    new ObjectCreation(new ObjectPath('page.meta.foo:bar.cObject', 'foo:bar.cObject'), new Scalar("TEXT"), 3),
                    new Assignment(new ObjectPath('page.meta.foo:bar.cObject.value', 'foo:bar.cObject.value'), new Scalar("qux"), 4)
                ], 2
            )
        ], 1
    )
];