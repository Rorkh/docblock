<?php

namespace Zeantar\Docblock;

/**
 * Docblock class
 */
class Docblock
{
    /**
     * Lines of docblock
     *
     * @var array
     */
    private array $lines = [];

    /**
     * Dictionary of tags
     *
     * @var array<string, DocblockTag>
     */
    private array $tagDictionary = [];

    /**
     * Docblock summary
     *
     * @var string|null
     */
    private ?string $summary = null;

    /**
     * Docblock description
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * Assigns line as summary or adds to description
     *
     * @param DocblockLine $line
     * @return void
     */
    private function appendStrings(DocblockLine $line): void
    {
        if (!$line->isText()) {
            return;
        }

        if (isset($this->summary)) {
            $this->description .= $line->getContent() . "\n";
        } else {
            $this->summary = $line->getContent();
        }
    }

    /**
     * Populates tags dictionary
     * Assigns reference to tag as value and tag name as key
     *
     * @return void
     */
    private function populateTags(): void
    {
        foreach ($this->lines as $line) {
            $tag = $line->getTag();
            if ($tag) {
                $this->tagDictionary[$tag->getName()] = &$tag;
            }
            unset($tag);
        }
    }

    private function __construct(string $content)
    {
        $rawLines = explode("\n", $content);

        foreach ($rawLines as $rawLine) {
            $line = DocblockLine::fromString($rawLine);
            $this->lines[] = $line;

            $this->appendStrings($line);
        }

        $this->populateTags();
    }

    /**
     * Returns raw docblock
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode("\n", $this->lines);
    }

    /**
     * Returns summary of docblock
     *
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * Returns description of docblock
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns line at index
     *
     * @param integer $idx
     * @return DocblockLine|null
     */
    public function getLine(int $idx): ?DocblockLine
    {
        return $this->lines[$idx] ?? null;
    }

    /**
     * Returns array of docblock lines
     *
     * @return array<DocblockLine>
     */
    public function getLines(): array
    {
        return $this->lines;
    }
    
    /**
     * Returns docblock tag by name
     *
     * @param string $name
     * @return DocblockTag|null
     */
    public function getTag(string $name): ?DocblockTag
    {
        return $this->tagDictionary[$name] ?? null;
    }

    /**
     * Returns new docblock object from reflection
     *
     * @param mixed $reflection
     * @throws \RuntimeException if passed reflection has no getDocComment method
     * @return Docblock|null
     */
    public static function fromReflection($reflection): ?Docblock
    {
        if (!method_exists($reflection, 'getDocComment')) {
            throw new \RuntimeException('Can\'t create docblock from reflection with no getDocComment method.');
        }
        $docComment = $reflection->getDocComment();
        if (!$docComment) {
            return null;
        }
        return new self($docComment);
    }

    /**
     * Returns new docblock object from class
     *
     * @param string $className
     * @return Docblock|null
     */
    public static function fromClass(string $className): ?Docblock
    {
        $class = new \ReflectionClass($className);
        return self::fromReflection($class);
    }

    /**
     * Returns new docblock object from class method
     *
     * @param string $className
     * @param string $methodName
     * @return Docblock|null
     */
    public static function fromMethod(string $className, string $methodName): ?Docblock
    {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        return self::fromReflection($method);
    }

    /**
     * Returns new docblock object from class property
     *
     * @param string $className
     * @param string $propertyName
     * @return Docblock|null
     */
    public static function fromProperty(string $className, string $propertyName): ?Docblock
    {
        $class = new \ReflectionClass($className);
        $property = $class->getProperty($propertyName);
        return self::fromReflection($property);
    }
}
