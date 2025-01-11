<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ImageBlobTest extends TestCase
{
    private Dumbastro\FitsPhp\ImageBlob $imageBlob;
    private Dumbastro\FitsPhp\Fits $fits;
    private Dumbastro\FitsPhp\FitsHeader $header;

    protected function setUp(): void
    {
        $this->fits = new Dumbastro\FitsPhp\Fits(__DIR__ . '/test_orion.fit');
        $this->header = $this->fits->fitsHeader;
        $blob = $this->fits->imageBlob;

        $this->imageBlob = new Dumbastro\FitsPhp\ImageBlob($this->header, $blob);
    }

    public function testBitpixValue(): void
    {
        $this->assertSame($this->imageBlob->bitpix->value, 16);
    }

    public function testDataBitsLength(): void
    {
        $this->assertSame($this->imageBlob->dataBits, 16*2448*1669*3);
    }

    public function testIsColor(): void
    {
        $mono = new Dumbastro\FitsPhp\Fits(__DIR__ . '/test_mono.fit');
        $imageBlob = new Dumbastro\FitsPhp\ImageBlob($mono->fitsHeader, $mono->imageBlob);

        $this->assertFalse($imageBlob->isColor);
        $this->assertTrue($this->imageBlob->isColor);

        $this->assertEquals($imageBlob->naxis3, 1);
    }
}
