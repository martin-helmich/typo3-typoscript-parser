<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\MultilineComment;

return [
    new MultilineComment('/*
Hello
World
*/', 1),
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Assignment(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        new \Helmich\TypoScriptParser\Parser\AST\Scalar('bar'),
        5
    ),
];