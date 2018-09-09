<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'foo/', 'typoscript', null, 1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'bar/', '', null, 2
    ),
];