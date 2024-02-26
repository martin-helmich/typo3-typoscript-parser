<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

use PhpParser\Node\Stmt\Nop;

/**
 * Helper class for quickly building AST nodes
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class Builder
{
    private Operator\Builder $operatorBuilder;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->operatorBuilder = new Operator\Builder();
    }

    /**
     * @psalm-param Statement[] $if
     * @psalm-param Statement[] $else
     */
    public function condition(string $condition, array $if, array $else, int $line): ConditionalStatement
    {
        return new ConditionalStatement($condition, $if, $else, $line);
    }

    public function comment(string $comment, int $line): Comment
    {
        return new Comment($comment, $line);
    }

    public function multilineComment(string $comment, int $line): MultilineComment
    {
        return new MultilineComment($comment, $line);
    }

    public function nop(int $line): NopStatement
    {
        return new NopStatement($line);
    }

    public function includeDirectory(string $directory, ?string $extensions, ?string $condition, int $line): DirectoryIncludeStatement
    {
        return new DirectoryIncludeStatement($directory, $extensions, $condition, $line);
    }

    public function includeFile(string $file, bool $newSyntax, ?string $condition, int $line): FileIncludeStatement
    {
        return new FileIncludeStatement($file, $newSyntax, $condition, $line);
    }

    /**
     * @psalm-param Statement[] $statements
     */
    public function nested(ObjectPath $path, array $statements, int $line): NestedAssignment
    {
        return new NestedAssignment($path, $statements, $line);
    }

    public function scalar(string $value): Scalar
    {
        return new Scalar($value);
    }

    public function path(string $absolute, string $relative): ObjectPath
    {
        return new ObjectPath($absolute, $relative);
    }

    public function op(): Operator\Builder
    {
        return $this->operatorBuilder;
    }
}
