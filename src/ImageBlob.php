<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

use Dumbastro\FitsPhp\Exceptions\InvalidBitpixValue;
use Dumbastro\FitsPhp\Bitpix;
use Jcupitt\Vips;

class ImageBlob
{
    private string $blob;
    private FitsHeader $header;
    public readonly Bitpix $bitpix;
    public readonly int $dataBits;
    public readonly int $width;
    public readonly int $height;
    public readonly ?int $naxis3;
    public readonly bool $isColor;

    /**
    * @throws InvalidBitpixValue
    * @todo Don't assume that NAXIS = 2...
    */
    public function __construct(FitsHeader $header, string $blob)
    {
        $this->header = $header;
        $this->blob = $blob;
        $bitpix = (int) $this->header->getKeywordValue('BITPIX');
        $this->bitpix = Bitpix::tryFrom($bitpix) ?? throw new InvalidBitpixValue($bitpix);
        $this->width = (int) trim($this->header->getKeywordValue('NAXIS1'));
        $this->height = (int) trim($this->header->getKeywordValue('NAXIS2'));
        $naxis3 = null;
        $dataBits = abs($this->bitpix->value) * $this->width * $this->height;

        $naxis = (int) trim($this->header->getKeywordValue('NAXIS'));

        // Color image (right?)
        if ($naxis === 3) {
            $naxis3 = (int) trim($this->header->getKeywordValue('NAXIS3'));
            $dataBits *= $naxis3;
        }

        $this->naxis3 = $naxis3 ?? 1;
        $this->dataBits = $dataBits;

        $this->isColor = $this->naxis3 === 3;
    }
    /**
    * Returns a generator that yields image data
    * pixel by pixel
    *@todo Conversion from 16 to 8-bit?
    *      This won't work with mono images...
    */
    public function pixels(?Channel $ch = null): \Generator
    {
        $n = 0;
        $pixel = [];
        $pixBytes = abs($this->bitpix->value) / 8;

        // Convert char to integer value
        for ($i = 0; $i < strlen($this->blob); $i++) {
            $value = unpack(
                format: 'C',
                string: $this->blob[$i]
            )[1];

            //$value = floor($value / 256);

            if ($i + $pixBytes <= strlen($this->blob) - 1) {
                $value += unpack(
                    format: 'C',
                    string: $this->blob[$i + $pixBytes - 1]
                )[1];
            }

            $pixel[$n] = $value;
            $n++;
            if ($n === $this->naxis3) {
                $n = 0;
                yield $pixel;
            }
        }
    }
    /**
    *@todo Assumes 8-bit
    */
    public function pixelsMono(): \Generator
    {
        // Convert char to integer value
        for ($i = 0; $i < strlen($this->blob); $i++) {
            $value = unpack(
                format: 'C',
                string: $this->blob[$i]
            )[1];

            yield [$value, $value, $value];
        }
    }

    public function dataBytes(): \Generator
    {
        for ($i = 0; $i < strlen($this->blob); $i++) {
            yield $this->blob[$i];
        }
    }
    /**
    * Convert to GD image
    * @todo Throw exception if gd fails?
    */
    public function toGdImage(): \GdImage
    {
        $image = $this->createGdImage();

        $x = 1;
        $y = 1;

        $pixels = $this->isColor ? $this->pixels() : $this->pixelsMono();

        foreach ($pixels as $k => $pixel) {
            [$r,$g, $b] = $pixel;

            // TODO this shouldn't be necessary...
            /*
            if ($r > 255) $r = 255;
            if ($g > 255) $g = 255;
            if ($b > 255) $b = 255;
            */

            $pixel = [$r, $g, $b];

            $this->gdPixel($image, $x, $y, $pixel);
            // Change row after reaching image width
            if ($x === $this->width + 1) {
                $y++;
                $x = 1;
            }
            $x++;
        }

        return $image;
    }
    /**
    * @param int[] $rgb
    */
    private function gdPixel(\GdImage $gdImg, int $x, int $y, array $rgb): bool
    {
        $color = imagecolorallocate($gdImg, ...$rgb);

        return imagesetpixel($gdImg, $x, $y, $color);
    }

    private function createGdImage(): \GdImage|false
    {
        return imagecreatetruecolor($this->width, $this->height);
    }
    /**
    * Convert to SVG for display
    * @todo This assumes RGB and produces a gigantic file...
    * Note: pixels are treated as SVG rectangles, RGB values are
            extracted from bit values... (?!)
    */
    public function toSVG(): string
    {
        $svg = <<<SVG
            <svg version="1.1"
                width="{$this->width}"
                height="{$this->height}"
            xmlns="http://www.w3.org/2000/svg">

        SVG;
        $x = 1;
        $y = 1;
        $cols = 1;
        // Build SVG using 1x1 rectangles
        foreach ($this->pixels() as $k => $pixel) {
            // A pixel is a 3-element array if the image is RGB
            [$r, $g, $b] = $pixel;
            $r = $r / 2;
            $g = $g / 2;
            $b = $b / 2;
            // Change row after reaching image width
            if ($x === $this->width + 1) {
                $y++;
                $x = 1;
            }
            $svg .= "<rect x=\"$x\" y=\"$y\" width=\"1\" height=\"1\" fill=\"rgb($r, $g, $b)\" />\n";
            $x++;
        }
        $svg .= '</svg>';

        return $svg;
    }
}
