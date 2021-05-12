<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[globalVar = GP:foo=1]', [
        new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
            new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'), [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.bar', 'bar'),
                new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('1'),
                3
            ),
        ], 2
        ),
        new \Helmich\TypoScriptParser\Parser\AST\MultilineComment('/*
Hello
World
*/', 5),
    ], [
        new \Helmich\TypoScriptParser\Parser\AST\MultilineComment('/*
Hello
World
*/', 10),
            new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'), [
                new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                    new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo.bar', 'bar'),
                    new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('2'),
                    15
                ),
            ], 14
            ),
        ], 1
    ),
];