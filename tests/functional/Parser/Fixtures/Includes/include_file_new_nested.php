<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\FileIncludeStatement;

return [
    new ConditionalStatement(
        '[frontend.user.isLoggedIn]', [new FileIncludeStatement('foo.typoscript', true, '', 2)], [], 1
    ),
];