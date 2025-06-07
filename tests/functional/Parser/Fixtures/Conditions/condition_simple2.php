<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[globalVar = GP:foo=1]',
        [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
                new \Helmich\TypoScriptParser\Parser\AST\Scalar('bar'),
                2
            ),
        ],
        [],
        1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\NopStatement(4),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('baz', 'baz'),
        new \Helmich\TypoScriptParser\Parser\AST\Scalar('foo'),
        5
    ),
];