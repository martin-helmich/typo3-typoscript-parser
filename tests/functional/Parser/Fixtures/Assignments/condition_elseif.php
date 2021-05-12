<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[globalVar = GP:foo=1]', [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
            new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('bar'),
            2
        ),
    ], [], 1),
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[globalVar = GP:foo=2]', [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
            new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('bar2'),
            4
        ),
    ], [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
                new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('baz'),
                6
            ),
        ], 3
    ),
];