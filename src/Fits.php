<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

use Dumbastro\FitsPhp\Exceptions\{
    InvalidFitsException,
    InvalidPathException,
};

class Fits
{
    /**
    * @var resource $byteStream
    */
    private $byteStream;
    private string $path;
    public readonly int $size;

    /**
    * @throws InvalidFitsException, InvalidPathException
    * @todo Check path for reading/writing errors
    */
    public function __construct(string $path)
    {
        if (! is_readable($path) || ! is_file($path)) {
            throw new InvalidPathException("The path '$path' is not readable or is not a file.");
        }
        $this->byteStream = fopen($path, 'rb');
        $this->path = $path;
        $this->size = filesize($this->path);

        if (! $this->validate()) {
            throw new InvalidFitsException('The opened file is not a valid FITS image (invalid block size)');
        }
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
        if ($this->size % 2880 !== 0) {
            return false;
        }

        return true;
    }
    /**
    * @todo Return FitsHeader object
    * @return string
    */
    #[\ReturnTypeWillChange]
    public function header(): string
    {
        $contents = fread($this->byteStream, $this->size);
        $end = strpos($contents, 'END');
        // Determine minimum integer number of blocks including 'END' position
        $headerEnd = (($end - ($end % 2880)) / 2880 + 1) * 2880;

        return substr($contents, 0, $headerEnd);
    }

    public function fromPath(string $path): void
    {
        $this->byteStream = fopen($path, 'rb');
    }
}
