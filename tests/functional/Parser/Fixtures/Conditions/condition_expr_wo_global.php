<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        condition: '[5 in tree.rootLineIds || 10 in tree.rootLineIds]',
        ifStatements: [
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
        ],
        elseStatements: [],
        sourceLine: 1,
        terminator: \Helmich\TypoScriptParser\Parser\AST\ConditionalStatementTerminator::Unterminated,
    ),
];