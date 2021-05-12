<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[usergroup=6]', [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
            new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('bar'),
            2
        ),
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('bar', 'bar'),
            new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('baz'),
            3
        ),
    ], [], 1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('baz', 'baz'),
        new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('foo'),
        5
    ),
];