<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

final class MultilineComment extends Statement
{
    public string $comment;

    public function __construct(string $comment, int $sourceLine)
    {
        parent::__construct($sourceLine);
        $sanitizedComment = preg_replace('/[\0\r\x0B\t]/', '', $comment);
        assert(is_string($sanitizedComment));

        $this->comment = $sanitizedComment;
    }

    public function getSubNodeNames(): array
    {
        return ['comment'];
    }
}
