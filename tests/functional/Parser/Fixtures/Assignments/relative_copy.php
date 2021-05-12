<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'), [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.bar', 'bar'),
            new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('baz'),
            2
        ),
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Copy(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.baz', 'baz'),
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.bar', '.bar'),
            3
        ),
    ],
        1
    ),
];