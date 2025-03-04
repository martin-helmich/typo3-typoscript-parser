<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[5 in tree.rootLineIds || 10 in tree.rootLineIds]', [
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
            new \Helmich\TypoScriptParser\Parser\AST\Scalar('bar'),
            2
        ),
        new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('bar', 'bar'),
            new \Helmich\TypoScriptParser\Parser\AST\Scalar('baz'),
            3
        ),
    ], [], 1, \Helmich\TypoScriptParser\Parser\AST\ConditionalStatementTerminator::End,
    ),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('baz', 'baz'),
        new \Helmich\TypoScriptParser\Parser\AST\Scalar('foo'),
        5
    ),
];