<?php

namespace Zeantar\Docblock;

/**
 * Docblock line class
 */
class DocblockLine
{
    /**
     * Raw (unprocessed) docblock line
     *
     * @var string
     */
    private string $rawString;

    /**
     * Content of docblock line (ignores markup)
     *
     * @var string|null
     */
    private ?string $content;

    /**
     * Tag on line
     *
     * @var DocblockTag|null
     */
    private ?DocblockTag $tag = null;

    private function __construct(string $rawString)
    {
        $this->rawString = $rawString;
        $this->content = explode('* ', $this->rawString)[1] ?? null;

        $this->tag = DocblockTag::fromLine($this) ?? null;
    }

    /**
     * Returns raw line
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getRaw();
    }

    /**
     * Returns raw line
     *
     * @return string
     */
    public function getRaw(): string
    {
        return $this->rawString;
    }

    /**
     * Returns content of docblock line
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        $content = $this->content;
        if (is_null($content) || trim($content) == '') {
            return null;
        }
        if (str_ends_with($content, '*/')) {
            $content = rtrim($content, " */");
        }
        return $content;
    }

    /**
     * Returns tag on line or null if not present
     *
     * @return DocblockTag|null
     */
    public function getTag(): ?DocblockTag
    {
        return $this->tag;
    }

    /**
     * Returns if line has a tag
     *
     * @return boolean
     */
    public function hasTag(): bool
    {
        return !is_null($this->tag);
    }

    /**
     * Returns if line is empty (has no content)
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return is_null($this->getContent());
    }

    /**
     * Returns if line is a text (not a tag and not empty)
     *
     * @return boolean
     */
    public function isText()
    {
        return !$this->hasTag() && !$this->isEmpty();
    }

    /**
     * Returns if line is a closure (*\ or /**)
     *
     * @return boolean
     */
    public function isClosure(): bool
    {
        $trimmedString = trim($this->rawString);
        return $trimmedString === '*/' || $trimmedString === '/**';
    }

    /**
     * Creates and returns new docblock line object from raw line
     *
     * @param string $string
     * @return DocblockLine
     */
    public static function fromString(string $string): DocblockLine
    {
        return new self($string);
    }
}
