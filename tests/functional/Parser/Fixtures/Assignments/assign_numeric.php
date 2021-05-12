<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\NestedAssignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("foo", "foo"),
        [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("foo.0", "0"),
                new \Helmich\TypoScriptParser\Parser\AST\ScalarValue("hello"),
                2
            ),
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath("foo.1", "1"),
                new \Helmich\TypoScriptParser\Parser\AST\ScalarValue("world"),
                3
            ),
        ],
        1
    )
];