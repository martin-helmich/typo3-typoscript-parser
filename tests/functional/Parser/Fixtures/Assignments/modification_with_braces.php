<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\NopStatement;

return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Modification(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        new \Helmich\TypoScriptParser\Parser\AST\Operator\ModificationCall(
            'appendString',
            'some string with () braces in it'
        ),
        1
    ),
    new NopStatement(2)
];
