<?php

namespace Zeantar\Docblock;

/**
 * Docblock tag class
 */
class DocblockTag
{
    private function __construct(private readonly string $name, private readonly ?string $value)
    {}

    /**
     * Returns name of tag
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns value of tag or null if not present
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Returns new tag object from line
     *
     * @param DocblockLine $line
     * @return DocblockTag|null
     */
    public static function fromLine(DocblockLine $line): ?DocblockTag
    {
        $content = $line->getContent();
        if (is_null($content)) {
            return null;
        }
        return self::fromContent($content);
    }

    /**
     * Parses and returns new tag object from raw line
     *
     * @param string $content
     * @return DocblockTag|null
     */
    public static function fromContent(string $content): ?DocblockTag
    {
        if (!str_contains($content, '@')) {
            return null;
        }

        static $STAGE_PRETAG = 0;
        static $STAGE_TAGNAME = 1;
        static $STAGE_PREVALUE = 2;
        static $STAGE_VALUE = 3;

        $stage = $STAGE_PRETAG;
        $name = $value = null;

        for ($i = 0; $i < strlen($content); $i++) {
            $char = $content[$i];
            
            if ($char == '@') {
                $stage = $STAGE_TAGNAME;
                continue;
            } elseif ($char == ' ' && $stage !== $STAGE_VALUE) {
                $stage = $STAGE_PREVALUE;
                continue;
            } elseif (ctype_alnum($char) && $stage == $STAGE_PREVALUE) {
                $stage = $STAGE_VALUE;
            }

            switch ($stage) {
                case $STAGE_TAGNAME:
                    $name .= $char;
                    break;
                case $STAGE_VALUE:
                    $value .= $char;
                    break;
            }
        }

        return new self($name, $value);
    }
}