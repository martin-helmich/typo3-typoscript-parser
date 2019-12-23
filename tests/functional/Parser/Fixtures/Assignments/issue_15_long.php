<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new NestedAssignment(new ObjectPath('page', 'page'), [
        new ObjectCreation(new ObjectPath('page.20', '20'), new Scalar('USER'), 2),
        new NestedAssignment(new ObjectPath('page.20', '20'), [
            new Assignment(new ObjectPath('page.20.userFunc', 'userFunc'), new Scalar('TYPO3\CMS\Extbase\Core\Bootstrap->run'), 4),
            new NestedAssignment(new ObjectPath('page.20.switchableControllerActions', 'switchableControllerActions'), [
                new NestedAssignment(new ObjectPath('page.20.switchableControllerActions.{$plugin.tx_foo.settings.bar.controllerName}', '{$plugin.tx_foo.settings.bar.controllerName}'), [
                    new Assignment(
                        new ObjectPath('page.20.switchableControllerActions.{$plugin.tx_foo.settings.bar.controllerName}.1', '1'),
                        new Scalar('{$plugin.tx_foo.settings.bar.actionName}'),
                        7
                    )
                ], 6)
            ], 5)
        ], 3)
    ], 1)
];