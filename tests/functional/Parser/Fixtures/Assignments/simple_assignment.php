<?php declare(strict_types=1);
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\ScalarValue;

return [
    new Assignment(
        new ObjectPath('foo', 'foo'),
        new ScalarValue('bar'),
        1
    ),
    new Assignment(
        new ObjectPath('bar', 'bar'),
        new ScalarValue(''),
        2
    ),
];