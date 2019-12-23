<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Delete(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        1
    ),
];