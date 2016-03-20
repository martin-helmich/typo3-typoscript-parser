<?php
return [
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'foo/', 'typoscript', 1
    ),
    new \Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement(
        'bar/', '', 2
    ),
];