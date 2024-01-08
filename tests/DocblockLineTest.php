<?php

declare (strict_types = 1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Zeantar\Docblock\DocblockLine;

final class DocblockLineTest extends TestCase
{
    #[TestDox('Docblock line creation')]
    public function testDocblockLineCreation(): void
    {
        $line = DocblockLine::fromString('  * Hello world');
        $this->assertNotNull($line);
    }

    #[TestDox('Docblock line parsing (raw/content)')]
    #[Depends('testDocblockLineCreation')]
    public function testDocblockLineParsing(): void
    {
        $string = '  * Hello world';
        $line = DocblockLine::fromString($string);

        $this->assertSame($string, $line->getRaw());
        $this->assertSame('Hello world', $line->getContent());
    }

    #[TestDox('Docblock line tag fetching')]
    #[Depends('testDocblockLineParsing')]
    public function testDocblockLineTagFetching(): void
    {
        $line = DocblockLine::fromString('  * @todo Better parser');
        $this->assertTrue($line->hasTag());

        $tag = $line->getTag();

        $this->assertNotNull($tag);
        $this->assertSame($tag->getName(), 'todo');
        $this->assertSame($tag->getValue(), 'Better parser');
    }

    protected static function getEmptyLines(): array
    {
        return ['*', ' *', ' * ', "\t*", "\t*\t", " \t *", "\t*\t ", "\t *\t "];
    }

    public static function isEmptyProvider(): array
    {
        $emptyLines = self::getEmptyLines();

        return [
            'empty lines' => $emptyLines,
        ];
    }

    #[DataProvider('isEmptyProvider')]
    #[TestDox('Docblock line empty test')]
    #[Depends('testDocblockLineTagFetching')]
    public function testDocblockEmptyQualification(string $emptyLine)
    {
        $line = DocblockLine::fromString($emptyLine);
        $this->assertTrue($line->isEmpty());
    }

    protected static function getTagLines()
    {
        return ['* @tag', '* @since 1984', '*@alsotag', "\t@tabbedtag"];
    }

    protected static function getTextLines()
    {
        return ['* bonjour', '* s o   me  c   ryptiк me!ssage', '*\tSummary'];
    }

    public static function isNotEmptyProvider(): array
    {
        $tagLines = self::getTagLines();
        $textLines = self::getTextLines();

        return [
            'tag lines' => $tagLines,
            'text lines' => $textLines
        ];
    }

    #[DataProvider('isNotEmptyProvider')]
    #[TestDox('Docblock line not empty test')]
    #[Depends('testDocblockLineTagFetching')]
    public function testDocblockNotEmptyQualification(string $notEmptyLine)
    {
        $line = DocblockLine::fromString($notEmptyLine);
        $this->assertFalse($line->isEmpty());
    }

    public static function getClosureLines(): array
    {
        return ['*/', '/**'];
    }

    public static function isClosureProvider()
    {
        return ['closures' => self::getClosureLines()];
    }

    #[DataProvider('isClosureProvider')]
    #[TestDox('Docblock line is closure test')]
    #[Depends('testDocblockLineTagFetching')]
    public function testDocblockIsClosureQualification(string $closureLine)
    {
        $line = DocblockLine::fromString($closureLine);
        $this->assertTrue($line->isClosure());
    }

    public static function isNotClosureProvider()
    {
        $tagLines = self::getTagLines();
        $textLines = self::getTextLines();
        $emptyLines = self::getEmptyLines();

        return [
            'tag lines' => $tagLines,
            'text lines' => $textLines,
            'empty lines' => $emptyLines,
        ];
    }

    #[DataProvider('isNotClosureProvider')]
    #[TestDox('Docblock line is not closure test')]
    #[Depends('testDocblockLineTagFetching')]
    public function testDocblockIsNotClosureQualification(string $closureLine)
    {
        $line = DocblockLine::fromString($closureLine);
        $this->assertFalse($line->isClosure());
    }

    public static function isNotTextProvider()
    {
        $tagLines = self::getTagLines();
        $closureLines = self::getClosureLines();
        $emptyLines = self::getEmptyLines();

        return [
            'tag lines' => $tagLines,
            'closure lines' => $closureLines,
            'empty lines' => $emptyLines,
        ];
    }

    #[DataProvider('isNotTextProvider')]
    #[TestDox('Docblock line is not text test')]
    #[Depends('testDocblockLineTagFetching')]
    public function testDocblockIsNotTextQualification(string $textLine)
    {
        $line = DocblockLine::fromString($textLine);
        $this->assertFalse($line->isText());
    }

    public static function isTextProvider()
    {
        $textLines = self::getTextLines();

        return [
            'text lines' => $textLines
        ];
    }

    #[DataProvider('isTextProvider')]
    #[TestDox('Docblock line is text test')]
    #[Depends('testDocblockLineTagFetching')]
    public function testDocblockIsTextQualification(string $textLine)
    {
        $line = DocblockLine::fromString($textLine);
        $this->assertTrue($line->isText());
    }
}
