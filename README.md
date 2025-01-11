# FitsPHP - A dumb FITS image library for PHP

FitsPHP (or `fits-php`) is a dumb and crappy "library" to somehow interact with the FITS image format used in astronomy.  
The library tries to implement a reasonable subset of the [FITS standard](https://fits.gsfc.nasa.gov/fits_standard.html). Of course, it all depends on the definition of 'reasonable'...

It should allow for the image to be displayed in a browser or even a terminal (yeah, right...).

The automatically generated documentation for the API is available here: [https://dumbastro.github.io/fits-php-docs/api](https://dumbastro.github.io/fits-php-docs/api).

## Dependencies

FitsPHP relies on [`php-vips`](https://github.com/libvips/php-vips), the PHP binding for [`libvips`](https://www.libvips.org/), to actually get usable image data from the FITS file. Using `php-vips` (instead of GD, for example) is convenient because it supports reading from FITS files natively (since `libvips` [does it](https://www.libvips.org/API/current/VipsForeignSave.html#vips-fitsload)) and is quite fast, being based on a <abbr title="Foreign Function Interface">FFI</abbr> layer over the actual C library (if I understand correctly).

In order for `php-vips` to work, the `libvips` C library should be installed. On a Linux system, it's usually available via the distribution's package manager.

For example, for Debian/Ubuntu:

```bash
sudo apt install libvips-dev libvips-tools
```

Refer to the [official documentation](https://www.libvips.org/install.html) for more details.

Also, the `zend.max_allowed_stack_size` variable in `php.ini` should be set to `-1`:

```ini
zend.max_allowed_stack_size = -1
```

## Features

FitsPHP, ideally, should provide some convenience methods for some very basic image processing, e.g. simple stretching algorithms mostly useful for previewing and perhaps some statistics and histogram plots.

As it stands at the moment, it does the following things:

- Load the FITS image and store its header as a PHP object
- Read keyword records from the header and access values and comments
- Convert the image data to a `Jcupitt\Vips\Image` object and store it as a property of the main class (`Dumbastro\FitsPhp\Fits`)
- Save the FITS to PNG, JPEG and TIFF, thanks to `php-vips`.

More possible developments are sketched in the [TODO](#todo) section.

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

#### Retrieve the image blob

For a given FITS file, the image blob can be read, for example to do something with the "raw" bytes. The method `ImageBlob#dataBytes` returns a `Generator`.

```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;
use Dumbastro\FitsPhp\ImageBlob;

$fits = new Fits('./bubble_nebula.fit');
$blob = new ImageBlob($fits->fitsHeader, $fits->imageBlob);

foreach ($blob->dataBytes() as $pixel) {
    // Do something useful...
}
```

Alternatively, which should be much better, the `Vips\Image` object can be accessed from the main class:

```php
<?php

declare(strict_types=1);

$fits = new Dumbastro\FitsPhp\Fits('./bubble_nebula.fit');
$image = $fits->vipsImage;

// Inverse Fast Fourier Transform
$image->invfft();

// Use the vips image to do something clever...
```

#### Save to another format

Currently, the image can be saved to PNG, JPEG and TIFF, but since FitsPhp uses `php-vip`'s `Vips\Image::writeToFile` method, the original FITS file can be saved to any format supported by `libvips`. 

```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;

$fits = new Fits('./bubble_nebula.fit');
$fits->saveToPNG('/some/path/bubble_nebula.png');
$fits->saveToTIFF('/some/path/bubble_nebula.tiff');
```
#### Read a specific keyword value from the FITS header

In this example, `BITPIX` is read which represents the bit depth (bits per channel) of the image. The bitpix is also a property of the image blob, represented by a PHP `Enum`. Calling the `type()` method on the property returns a string with the following possible values (assuming the bitpix is valid in the first place):

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
use Dumbastro\FitsPhp\ImageBlob;

$fits = new Fits('./bubble_nebula.fit'); // Bit-depth is 32-bit unsigned (for example)
$fitsHeader = $fits->fitsHeader;
$bitpix = (int) $fitsHeader->getKeywordRecord('BITPIX')->value;

// Or, with the image blob
$blob = new ImageBlob($header, $fits->imageBlob);
echo $blob->bitpix->type(); //int32
```

## Limitations

Too many to write about them all at this point... Only 8-bit monochrome (grayscale) image data are somewhat supported,
meaning that the `ImageBlob#pixelsMono` method will convert string character bytes from the data stream
into 8-bit (0-255) decimal values, using `unpack()` (unfortunately...).  
However, the methods based on `php-vips`, such as `Fits#saveAsPNG`, were tested with 16-bit FITS images, both mono and RGB, and seem to be working properly.

For one-shot-colour images, it will not do any [demosacing](https://en.wikipedia.org/wiki/Demosaicing) (or _debayering_), of course, so if the image has not been already debayered, the displayed picture will be grayscale, with the [colour filter array](https://en.wikipedia.org/wiki/Color_filter_array) (CFA) visible as an overlayed pattern of grey squares (probably).

## TODO

- [x] Separate the main data table - i.e., actual image data - from the header (partly done? Who knows...)
- [x] Read keywords from the FITS header (COMMENT keywords could be buggy)
- [x] Read (8-bit) monochrome image pixels
- [x] Save the image to PNG, JPEG and TIFF
- [ ] Debayering?!
- [ ] Encode to base64 for browser display
- [ ] Implement basic stretching algorithms??
- [ ] Implement basic pixel statistics?!
- [ ] FITS extensions?
