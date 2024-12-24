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
}
 
