<?php
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new NestedAssignment(
        new ObjectPath('config.tx_extbase', 'config.tx_extbase'), [
            new NestedAssignment(
                new ObjectPath('config.tx_extbase.view', 'view'), [
                    new NestedAssignment(
                        new ObjectPath('config.tx_extbase.view.widget', 'widget'), [
                            new NestedAssignment(
                                new ObjectPath('config.tx_extbase.view.widget.TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper', 'TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper'), [
                                    new Assignment(
                                        new ObjectPath('config.tx_extbase.view.widget.TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper.templateRootPath', 'templateRootPath'),
                                        new Scalar('EXT:ext_key/Resources/Private/Templates'),
                                        6
                                    )
                                ],
                                5
                            )
                        ],
                        4
                    )
                ],
                2
            )
        ],
        1
    )
];