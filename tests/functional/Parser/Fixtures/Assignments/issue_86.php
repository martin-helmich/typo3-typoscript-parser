<?php

use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\NopStatement;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new NestedAssignment(
        new ObjectPath('templates.vendor/package', 'templates.vendor/package'),
        [
            new Assignment(
                new ObjectPath('templates.vendor/package.10', '10'),
                new Scalar('vendor/otherpackage/Resources/Private/Backend'),
                2,
            ),
        ],
        1,
    ),
];
