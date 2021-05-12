<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('bar', 'bar'),
        new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('baz'),
        1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Reference(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('baz', 'baz'),
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('bar', '.bar'),
        2
    ),
];