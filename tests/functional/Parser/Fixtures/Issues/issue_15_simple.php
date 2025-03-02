<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new Assignment(new ObjectPath('{$foo}', '{$foo}'), new Scalar('bar'), 1)
];