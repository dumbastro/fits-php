<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FitsHeaderTest extends TestCase
{
    private Dumbastro\FitsPhp\Fits $fits;
    private Dumbastro\FitsPhp\FitsHeader $header;
    
    protected function setUp(): void
    {
        $this->fits = new Dumbastro\FitsPhp\Fits(__DIR__ . '/test_orion.fit');
        $this->header = new Dumbastro\FitsPhp\FitsHeader($this->fits->headerBlock);
    }

    public function testKeywordValues(): void
    {
        $this->assertSame(
            trim($this->header->getKeywordValue('BITPIX')),
            '16'
        );
        $this->assertSame(
            trim($this->header->getKeywordValue('NAXIS')),
            '3'
        );
        $this->assertSame(
            trim($this->header->getKeywordValue('STACKCNT')),
            '86'
        );
    }

    public function testKeyword(): void
    {
        $keyword = $this->header->keyword('NAXIS1');

        // Probably useless... (covered by PHPStan)
        $this->assertInstanceOf(Dumbastro\FitsPhp\Keyword::class, $keyword);

        $this->assertEquals($keyword->value, 2448);

        $keyword = $this->header->keyword('EQUINOX');

        $this->assertEquals(trim($keyword->value), '2000.');
    }

    /**
    * @todo This fails for COMMENT keywords
    public function testToString(): void
    {
        $headerBlock = $this->fits->headerBlock;
        $headerString = $this->header->toString();

        $this->assertEquals($headerBlock, $headerString);
    }
    */
}
 
