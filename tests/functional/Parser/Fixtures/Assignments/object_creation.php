<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        new \Helmich\TypoScriptParser\Parser\AST\ScalarValue('TEXT'),
        1
    ),
];