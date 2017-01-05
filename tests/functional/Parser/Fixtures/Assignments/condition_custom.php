<?php

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new ConditionalStatement('[Foo\Bar\Custom = 42]', [
        new Assignment(new ObjectPath('foo', 'foo'), new Scalar('bar'), 2)
    ], [], 1)
];