<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\ScalarValue;

return [
    new NestedAssignment(new ObjectPath('page', 'page'), [
        new ObjectCreation(new ObjectPath('page.20', '20'), new ScalarValue('USER'), 2),
        new NestedAssignment(new ObjectPath('page.20', '20'), [
            new Assignment(new ObjectPath('page.20.userFunc', 'userFunc'), new ScalarValue('TYPO3\CMS\Extbase\Core\Bootstrap->run'), 4),
            new NestedAssignment(new ObjectPath('page.20.switchableControllerActions', 'switchableControllerActions'), [
                new NestedAssignment(new ObjectPath('page.20.switchableControllerActions.{$plugin.tx_foo.settings.bar.controllerName}', '{$plugin.tx_foo.settings.bar.controllerName}'), [
                    new Assignment(
                        new ObjectPath('page.20.switchableControllerActions.{$plugin.tx_foo.settings.bar.controllerName}.1', '1'),
                        new ScalarValue('{$plugin.tx_foo.settings.bar.actionName}'),
                        7
                    )
                ], 6)
            ], 5)
        ], 3)
    ], 1)
];