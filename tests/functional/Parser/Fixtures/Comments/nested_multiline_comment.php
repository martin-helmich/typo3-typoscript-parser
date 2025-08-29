<?php declare(strict_types=1);

return [
    new \Helmich\TypoScriptParser\Parser\AST\MultilineComment('/*
    temp {
        foo = 1
    }
*/', 1),
];