<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        new \Helmich\TypoScriptParser\Parser\AST\Scalar('bar'),
        1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Copy(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('bar', 'bar'),
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        2
    )
];