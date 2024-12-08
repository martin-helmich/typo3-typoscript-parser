<?php declare(strict_types=1);
return [
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'foo/', null, "Your\\Custom\\ClassOne", 1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'foo/', 'typoscript', "Your\\Custom\\ClassTwo", 2
    ),
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'foo/', 'typoscript', "Your\\Custom\\ClassThree", 3
    ),
];