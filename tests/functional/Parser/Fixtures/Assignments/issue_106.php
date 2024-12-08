<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
        object: new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("a", "a"),
        statements: [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                object: new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("a.height", "height"),
                value: new \Helmich\TypoScriptParser\Parser\AST\Scalar(""),
                sourceLine: 2,
            ),
        ],
        sourceLine: 1,
    )
];