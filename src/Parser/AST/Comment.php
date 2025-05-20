<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

final class Comment extends Statement
{
    public string $comment;

    public function __construct(string $comment, int $sourceLine)
    {
        parent::__construct($sourceLine);
        $this->comment = $comment;
    }

    public function getSubNodeNames(): array
    {
        return ['comment'];
    }
}
