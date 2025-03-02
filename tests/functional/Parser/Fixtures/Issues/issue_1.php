<?php declare(strict_types=1);

use Helmich\TypoScriptParser\Parser\AST\Comment;
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\NopStatement;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use Helmich\TypoScriptParser\Parser\AST\Statement;

return [
    new NestedAssignment(
        new ObjectPath('config.tx_extbase', 'config.tx_extbase'), [
            new NopStatement(2),
            new NestedAssignment(
                new ObjectPath('config.tx_extbase.view', 'view'), [
                    new Comment('# Configure where to look for widget templates', 4),
                    new NestedAssignment(
                        new ObjectPath('config.tx_extbase.view.widget', 'widget'), [
                            new NestedAssignment(
                                new ObjectPath('config.tx_extbase.view.widget.TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper', 'TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper'), [
                                    new Assignment(
                                        new ObjectPath('config.tx_extbase.view.widget.TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper.templateRootPath', 'templateRootPath'),
                                        new Scalar('EXT:ext_key/Resources/Private/Templates'),
                                        7
                                    )
                                ],
                                6
                            )
                        ],
                        5
                    )
                ],
                3
            )
        ], 2
    ),
];