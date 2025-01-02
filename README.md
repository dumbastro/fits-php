# FitsPHP - A dumb FITS image library for PHP

FitsPHP (or `fits-php`) is a dumb and crappy "library" to somehow interact with the FITS image format used in astronomy.  
The library tries to implement a reasonable subset of the [FITS standard](https://fits.gsfc.nasa.gov/fits_standard.html). Of course, it all depends on the definition of 'reasonable'...

It should allow for the image to be displayed in a browser or even a terminal (yeah, right...).

## Why

Why not? But seriously, this doesn't make any sense, you should never use it in any "real-world" situation...

## Usage

**NOTE: Not added to Packagist yet...**

Add the package with Composer:

```
composer require dumbastro/fits-php
```

then use classes from the `Dumbastro\FitsPhp` namespace.

### Examples

Retrieve the image blob for a given FITS file then do something with the bytes.  
The method `ImageBlob#pixels` returns a `Generator`.

```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;
use Dumbastro\FitsPhp\ImageBlob;

$fits = new Fits('bubble_nebula.fit');
$blob = new ImageBlob($fits->header(), $fits->imageBlob);

foreach ($blob->pixels() as $pixel) {
    // Do something useful...
}
```

or, save the image as a faulty, wrongly displayed PNG:


```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;

$fits = new Fits('bubble_nebula.fit');

$fits->saveAsPNG('/some/path/bubble_nebula.png');

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
$bitpix = (int) $fitsHeader->getKeywordRecord('BITPIX')->value;

// Or, with the image blob
$blob = new ImageBlob($header, $fits->imageBlob);
echo $blob->bitpix->type(); //int32

```

## Limitations

Too many to write about them all at this point... Only 8-bit image data are somewhat supported,
meaning that the `ImageBlob#pixels` method will convert string character bytes from the data stream
into 8-bit (0-255) decimal values, using `unpack()` (unfortunately...).  

For colour images, it will not do any [demosacing](https://en.wikipedia.org/wiki/Demosaicing) (or _debayering_), of course, so the displayed picture will be grayscale, with the [colour filter array](https://en.wikipedia.org/wiki/Color_filter_array) (CFA) visible as an overlayed pattern of grey squares, as in this example (detail):

<img>

The `Fits#saveAsPNG` method is very slow, I'm not sure to what extent I'll be able to optimize it...

## TODO

- [x] Separate the main data table - i.e., actual image data - from the header (partly done? Who knows...)
- [x] Read keywords from the FITS header (COMMENT keywords could be buggy)
- [x] Save the image to PNG and/or JPG (partly done and buggy)
- [ ] Encode to (PNG? JPEG?) base64 for browser display
- [ ] Actually display the image in the standard output
- [ ] History keywords?
- [ ] Support 16-bits images
- [ ] Manipulate the bits using basic processing algorithms??
- [ ] FITS extensions?
