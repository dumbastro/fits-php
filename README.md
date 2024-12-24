# FitsPHP - A dumb FITS image library for PHP

FitsPHP (or `fits-php`) is a dumb and crappy "library" to somehow interact with the FITS image format used in astronomy.  
The library tries to implement a reasonable subset of the [FITS standard](https://fits.gsfc.nasa.gov/fits_standard.html). Of course, it all depends on the definition of 'reasonable'...

It should allow for the image to be displayed in a browser or even a terminal (yeah, right...).

## Why

Why not? But seriously, this doesn't make any sense, you should never use it in any circumstance whatsoever!

## Usage

Add the package with Composer:

```
composer require dumbastro/fits-php
```

then use classes from the `Dumbastro\FitsPhp` namespace. More info in the documentation.

### Examples

Retrieve the image blob for a given FITS file then do something with the bytes.  
The method `ImageBlob::dataBytes` returns a `Generator`.

```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;
use Dumbastro\FitsPhp\ImageBlob;

$fits = new Fits('bubble_nebula.fit');
$blob = new ImageBlob($fits->header(), $fits->imageBlob);

foreach ($blob->dataBytes() as $byte) {
    // Do something useful...
}
```

Read a specific keyword value from the FITS header. In this example, `BITPIX` is read which represents the bit depth (bits per pixel) of the image. The bitpix is also a property of the image blob, represented by a PHP `Enum`. Calling the `type()` method on the property returns a string with the following possible values (assuming the bitpix is valid in the first place):

|Bitpix value|Type string|Interpretation|
-------------|-----------|--------------|
|    8      | `int8`  |Character or unsigned binary integer|
|   16      | `int16` |16 bit two's complement binary integer|
|   32      | `int32` |32 bit two's complement binary integer|
|   64      | `int64` |64 bit two's complement binary integer|
|   -32     |`float32`|IEEE single-precision floating point|
|   -64     |`float64`|IEEE double-precision floating point|

```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;
use Dumbastro\FitsPhp\FitsHeader;
use Dumbastro\FitsPhp\ImageBlob;

$fits = new Fits('bubble_nebula.fit'); // Bit-depth is 32-bit unsigned (for example)
$fitsHeader = new FitsHeader($fits->headerBlock);
$bitpix = (int) $fitsHeader->keyword('BITPIX')->value;

// Or, with the image blob
$blob = new ImageBlob($header, $fits->imageBlob);
echo $blob->bitpix->type(); //int32

```

## TODO

- [x] Separate the main data table (actual image data) from the header (partly done? Who knows...)
- [x] Read keywords from the FITS header (but consider values and comments could be more than 80-bytes long)
- [ ] History keywords?
- [ ] FITS extensions? What to do with NAXIS > 2?
- [ ] Actually display the image in the standard output (with SVG?)
- [ ] Save the image to PNG and JPG
- [ ] Manipulate the bits using basic processing algorithms??
