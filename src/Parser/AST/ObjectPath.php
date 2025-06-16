<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * An object path.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class ObjectPath implements Node
{
    /**
     * The relative object path, as specified in the source code.
     */
    public string $relativeName;

    /**
     * The absolute object path, as evaluated from parent nested statements.
     */
    public string $absoluteName;

    /**
     * Constructs a new object path.
     *
     * @param string $absoluteName The absolute object path.
     * @param string $relativeName The relative object path.
     */
    public function __construct(string $absoluteName, string $relativeName)
    {
        $this->absoluteName = $absoluteName;
        $this->relativeName = $relativeName;
    }

    public function depth(): int
    {
        return count(explode('.', $this->absoluteName));
    }

    /**
     * Builds the path to the parent object.
     *
     * @return ObjectPath The path to the parent object.
     */
    public function parent(): ObjectPath
    {
        $components = explode('.', $this->absoluteName);
        array_pop($components);

        if (count($components) === 0) {
            return new RootObjectPath();
        }
        return new self(implode('.', $components), $components[count($components) - 1]);
    }

    public function append(string $name): self
    {
        if ($name[0] === '.' && $name !== '.') {
            return new self($this->absoluteName . $name, $name);
        }
        return new self($this->absoluteName . '.' . $name, $name);
    }

    /**
     * @return string[]
     */
    public function getSubNodeNames(): array
    {
        return ['absoluteName', 'relativeName'];
    }
}
