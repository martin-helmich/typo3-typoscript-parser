<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\ScalarValue;

return [
    new ConditionalStatement('[Foo\Bar\Custom = 42]', [
        new Assignment(new ObjectPath('foo', 'foo'), new ScalarValue('bar'), 2)
    ], [], 1)
];