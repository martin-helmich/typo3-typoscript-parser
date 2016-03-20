<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        new \Helmich\TypoScriptParser\Parser\AST\Scalar('TEXT'),
        1
    )
];