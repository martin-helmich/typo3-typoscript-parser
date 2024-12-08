<?php

return [
    new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
        object: new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("a", "a"),
        statements: [
            new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
                object: new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("a.b", "b"),
                statements: [
                    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                        object: new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("a.b.c", "c"),
                        value: new \Helmich\TypoScriptParser\Parser\AST\Scalar(1),
                        sourceLine: 3,
                    )
                ],
                sourceLine: 2,
            ),
            new \Helmich\TypoScriptParser\Parser\AST\NopStatement(sourceLine: 5),
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                object: new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("a.d", "d"),
                value: new \Helmich\TypoScriptParser\Parser\AST\Scalar(2),
                sourceLine: 6,
            )
        ],
        sourceLine: 1,
    )
];