<?php
namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * Helper class for quickly building AST nodes
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class Builder
{
    /** @var Operator\Builder */
    private $operatorBuilder;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->operatorBuilder = new Operator\Builder();
    }

    /**
     * @param string      $condition
     * @param Statement[] $if
     * @param Statement[] $else
     * @param int         $line
     * @return ConditionalStatement
     */
    public function condition($condition, array $if, array $else, $line)
    {
        return new ConditionalStatement($condition, $if, $else, $line);
    }

    /**
     * @param string $directory
     * @param string $extensions
     * @param int    $line
     * @return DirectoryIncludeStatement
     */
    public function includeDirectory($directory, $extensions, $line)
    {
        return new DirectoryIncludeStatement($directory, $extensions, $line);
    }

    /**
     * @param string $file
     * @param int    $line
     * @return FileIncludeStatement
     */
    public function includeFile($file, $line)
    {
        return new FileIncludeStatement($file, $line);
    }

    /**
     * @param ObjectPath  $path
     * @param Statement[] $statements
     * @param int         $line
     * @return NestedAssignment
     */
    public function nested(ObjectPath $path, array $statements, $line)
    {
        return new NestedAssignment($path, $statements, $line);
    }

    /**
     * @param string $value
     * @return Scalar
     */
    public function scalar($value)
    {
        return new Scalar($value);
    }

    /**
     * @param string $absolute
     * @param string $relative
     * @return ObjectPath
     */
    public function path($absolute, $relative)
    {
        return new ObjectPath($absolute, $relative);
    }

    /**
     * @return Operator\Builder
     */
    public function op()
    {
        return $this->operatorBuilder;
    }
}
