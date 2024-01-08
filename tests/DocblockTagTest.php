<?php 

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Depends;
use Zeantar\Docblock\Docblock;
use Zeantar\Docblock\DocblockLine;
use Zeantar\Docblock\DocblockTag;

final class DocblockTagTest extends TestCase
{
    #[TestDox('Docblock tag creation from content')]
    public function testDocblockTagCreationFromContent(): void
    {
        $docblockTag = DocblockTag::fromContent('@tag smth');
        $this->assertNotNull($docblockTag);
    }

    #[TestDox('Docblock tag creation from line')]
    #[Depends('testDocblockTagCreationFromContent')]
    public function testDocblockTagCreationFromLine(): void
    {
        $docblockLine = DocblockLine::fromString('* @tag smth');
        $docblockTag = DocblockTag::fromLine($docblockLine);
        $this->assertNotNull($docblockTag);
    }

    public static function docblockTagProvider()
    {
        return [
            'without value' => ['@api', 'api', null],
            'with value' => ['@since 1.0', 'since', '1.0'],
            'value with space' => ['@author Ryan Gosling', 'author', 'Ryan Gosling']
        ];
    }

    #[DataProvider('docblockTagProvider')]
    #[TestDox('Docblock tag parse (name/value)')]
    #[Depends('testDocblockTagCreationFromContent')]
    public function testDocblockTagParse(string $content, string $name, ?string $value): void
    {
        $docblockTag = DocblockTag::fromContent($content);
        $this->assertEquals($name, $docblockTag->getName());
        $this->assertEquals($value, $docblockTag->getValue());
    }
}