<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'), [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.bar', 'bar'),
            new \Helmich\TypoScriptParser\Parser\AST\Scalar('baz'),
            2
        ),
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Reference(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.baz', 'baz'),
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.bar', '.bar'),
            3
        ),
    ],
        1
    ),
];