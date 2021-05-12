<?php declare(strict_types=1);
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\ScalarValue;

return [
    new NestedAssignment(
        new ObjectPath('page', 'page'), [
            new NestedAssignment(
                new ObjectPath('page.meta', 'meta'), [
                    new ObjectCreation(new ObjectPath('page.meta.foo:bar.cObject', 'foo:bar.cObject'), new ScalarValue("TEXT"), 3),
                    new Assignment(new ObjectPath('page.meta.foo:bar.cObject.value', 'foo:bar.cObject.value'), new ScalarValue("qux"), 4)
                ], 2
            )
        ], 1
    )
];