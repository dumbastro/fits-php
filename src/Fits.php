<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

use Dumbastro\FitsPhp\Exceptions\{
    InvalidFits,
    InvalidPath,
};
use Jcupitt\Vips;

class Fits
{
    private string $contents;
    private string $path;
    public readonly FitsHeader $fitsHeader;
    public readonly int $size;
    public readonly string $headerBlock;
    public readonly string $imageBlob;
    public readonly Vips\Image $vipsImage;

    /**
    * Construct a Fits object
    * @param string $path The full path to the FITS file
    * @throws InvalidFits, InvalidPath
    * @todo Check path for reading/writing errors
    */
    public function __construct(string $path)
    {
        if (! is_readable($path) || ! is_file($path)) {
            throw new InvalidPath("The path '$path' is not readable or is not a file.");
        }
        $this->contents = file_get_contents($path);
        $this->path = $path;
        $this->size = filesize($this->path);

        if (! $this->validate()) {
            throw new InvalidFits('The opened file is not a valid FITS image (invalid block size)');
        }

        $this->headerBlock = $this->extractHeader();

        $this->fitsHeader = new FitsHeader($this->headerBlock);
        $this->imageBlob = $this->extractImageBlob();
        $this->vipsImage = Vips\Image::newFromFile($path, ['access' => 'sequential']);
    }
    /**
    * Validate the given FITS file based on block sizes
    *
    * From the FITS standard spec: 
    *   > "Each FITS structure shall consist of an integral number of
    *    FITS blocks, which are each 2880 bytes (23040 bits) in length."
    *   (_Definition of the Flexible Image Transport System (FITS)_, ch. 3, par. 3.1)
    */
    public function validate(): bool
    {
        return (bool) $this->size % 2880 !== 0;
    }
    /**
    * Extract the FITS header block as a string
    */
    private function extractHeader(): string
    {
        
        $end = strpos($this->contents, ' END');
        // Determine minimum integer number of blocks including 'END' position
        $headerEnd = (($end - ($end % 2880)) / 2880 + 1) * 2880;

        return substr($this->contents, 0, $headerEnd);
    }
    /**
    * Extract the FITS image blob as a string;
    * it uses the NAXIS1 and NAXIS2 keywords
    * to compute the length of the main data table
    */
    private function extractImageBlob(): string
    {
        $naxis1 = (int)trim($this->fitsHeader->getKeywordValue('NAXIS1'));
        $naxis2 = (int)trim($this->fitsHeader->getKeywordValue('NAXIS2'));
        $naxis3 = (int)trim($this->fitsHeader->getKeywordValue('NAXIS3'));

        $blobEnd = $naxis1 * $naxis2; //* $naxis3;

        return substr(
            $this->contents,
            strlen($this->headerBlock),
            $blobEnd
        );
    }
    /**
    * @todo Write exception and check extension; PNG compression?
    */
    public function saveToPNG(string $path, int $compressionLevel = 9): void
    {
        $this->vipsImage->writeToFile($path);
    }
    /**
    * @todo Write exception and check extension
    */
    public function saveToJPG(string $path): void
    {
        $this->vipsImage->writeToFile($path);
    }
    /**
    * @todo Write exception and check extension
    */
    public function saveToTIFF(string $path): void
    {
        $this->vipsImage->writeToFile($path);
    }

    public function save(?string $path = null): void
    {
        // TODO
    }
}
