<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

use Dumbastro\FitsPhp\Exceptions\InvalidBitpixValue;
use Dumbastro\FitsPhp\Bitpix;

class ImageBlob
{
    private string $blob;
    private FitsHeader $header;
    public readonly Bitpix $bitpix;
    public readonly int $dataBits;

    /**
    * @throws InvalidBitpixValue
    * @todo Don't assume that NAXIS = 2...
    */
    public function __construct(FitsHeader $header, string $blob)
    {
        $this->header = $header;
        $this->blob = $blob;
        $bitpix = (int) $this->header->keyword('BITPIX')->value;
        $this->bitpix = Bitpix::tryFrom($bitpix) ?? throw new InvalidBitpixValue($bitpix);
        $naxis1 = (int) trim($this->header->keyword('NAXIS1')->value);
        $naxis2 = (int) trim($this->header->keyword('NAXIS2')->value);
        $this->dataBits = abs($this->bitpix->value) * $naxis1 * $naxis2;
    }
    /**
    * Returns a generator that yields image data
    * byte by byte
    */
    public function dataBytes(): \Generator
    {
        for ($i = 0; $i < strlen($this->blob); $i++) {
            yield $this->blob[$i];
        }
    }
    /**
    * Convert to PNG
    * @todo Manipulate bits and add PNG header to blob??
    *       Throw exception if gd fails?
    */
    public function toPNG(int $quality = -1): bool
    {
        $gdImg = imagecreatefromstring($this->blob);
        return imagepng($gdImg, quality: $quality);
    }
}
