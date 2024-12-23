<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FitsHeaderTest extends TestCase
{
    public function testKeywordValue(): void
    {
        $fits = new Dumbastro\FitsPhp\Fits(__DIR__ . '/test_orion.fit');
        $header = new Dumbastro\FitsPhp\FitsHeader($fits->headerBlock);

        $this->assertSame(
            trim($header->getKeywordValue('BITPIX')),
            '16'
        );
        $this->assertSame(
            trim($header->getKeywordValue('NAXIS')),
            '3'
        );
    }
}
 
