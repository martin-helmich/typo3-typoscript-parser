<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[globalVar = GP:foo=1]', [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
            new \Helmich\TypoScriptParser\Parser\AST\Scalar('bar'),
            2
        ),
    ], [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
                new \Helmich\TypoScriptParser\Parser\AST\Scalar('baz'),
                4
            ),
        ], 1
    ),
];