<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\Comment;

return [
    new \Helmich\TypoScriptParser\Parser\AST\Operator\Delete(
        new \Helmich\TypoScriptParser\Parser\AST\ObjectPath('foo', 'foo'),
        1
    ),
    new Comment('# Something', 1),
];