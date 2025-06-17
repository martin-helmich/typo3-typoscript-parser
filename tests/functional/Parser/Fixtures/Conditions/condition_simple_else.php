<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\ConditionalStatement(
        '[globalVar = GP:foo=1]',
        [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('one', 'one'),
                new \Helmich\TypoScriptParser\Parser\AST\Scalar('1'),
                2
            ),
        ],
        [
            new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
                new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('two', 'two'),
                new \Helmich\TypoScriptParser\Parser\AST\Scalar('2'),
                4
            ),
        ],
        1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\NopStatement(6),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('three', 'three'),
        new \Helmich\TypoScriptParser\Parser\AST\Scalar('3'),
        7
    ),
];