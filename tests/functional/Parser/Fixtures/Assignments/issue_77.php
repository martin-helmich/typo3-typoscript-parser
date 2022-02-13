<?php

use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;

return [
    new NestedAssignment(
        new ObjectPath('TCEFORM', 'TCEFORM'),
        [
            new NestedAssignment(
                new ObjectPath('TCEFORM.tt_content', 'tt_content'),
                [
                    new NestedAssignment(
                        new ObjectPath('TCEFORM.tt_content.space_before_class', 'space_before_class'),
                        [
                            new NestedAssignment(
                                new ObjectPath('TCEFORM.tt_content.space_before_class.altLabels', 'altLabels'),
                                [
                                    new Assignment(
                                        new ObjectPath('TCEFORM.tt_content.space_before_class.altLabels..', '.'),
                                        new Scalar('LLL:EXT:theme/Resources/Private/Language/locallang_db.xlf:general.noGap'),
                                        5,
                                    ),
                                ],
                                4,
                            )
                        ],
                        3,
                    )
                ],
                2,
            )
        ],
        1,
    )
];
