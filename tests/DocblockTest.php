<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Depends;
use Zeantar\Docblock\Docblock;

final class DocblockTest extends TestCase
{
    /** Single-line docblock object creation test */
    #[TestDox('Single-line docblock creation')]
    public function testSingleLineDocblockCreation(): void
    {
        $singleLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $this->assertNotNull($singleLineDocblock);
    }

    /** @todo */
    #[TestDox('Single-line line fetching and parsing (raw/content)')]
    #[Depends('testSingleLineDocblockCreation')]
    public function testSingleLineFetching(): void
    {
        $singleLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $line = $singleLineDocblock->getLine(0);

        $this->assertNotNull($line);
        
        $this->assertSame(count($singleLineDocblock->getLines()), 1);
        $this->assertSame($line->getRaw(), '/** @todo */');
        $this->assertSame($line->getContent(), '@todo');
    }

    /** Single-line summary fetching test */
    #[TestDox('Single-line summary fetching')]
    #[Depends('testSingleLineDocblockCreation')]
    public function testSingleLineSummaryFetching(): void
    {
        $singleLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $this->assertSame('Single-line summary fetching test', $singleLineDocblock->getSummary());
    }

    /**
     * Multiline docblock object creation test
    */
    #[TestDox('Multiline docblock creation')]
    public function testMultilineDocblockCreation(): void
    {
        $multiLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $this->assertNotNull($multiLineDocblock);
    }

    /**
     * Multiline line fetching and parsing test (raw/content)
     */
    #[TestDox('Multiline line fetching and parsing (raw/content)')]
    #[Depends('testMultilineDocblockCreation')]
    public function testMultilineFetching(): void
    {
        $multiLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $lines = $multiLineDocblock->getLines();

        $this->assertSame(count($lines), 3);

        $line = $multiLineDocblock->getLine(1);
        $this->assertSame($line->getRaw(), "     * Multiline line fetching and parsing test (raw/content)");
        $this->assertSame($line->getContent(), "Multiline line fetching and parsing test (raw/content)");
    }

    /**
     * Multiline summary fetching test
    */
    #[TestDox('Multiline summary fetching')]
    #[Depends('testMultilineDocblockCreation')]
    public function testMultilineSummaryFetching(): void
    {
        $multiLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $this->assertSame('Multiline summary fetching test', $multiLineDocblock->getSummary());
    }

    /**
     * Multiline summary fetching test
     * 
     * That's a long
     * description
    */
    #[TestDox('Multiline description fetching')]
    #[Depends('testMultilineDocblockCreation')]
    public function testMultilineDescriptionFetching(): void
    {
        $multiLineDocblock = Docblock::fromMethod(__CLASS__, __FUNCTION__);
        $this->assertSame("That's a long\ndescription\n", $multiLineDocblock->getDescription());
    }
}