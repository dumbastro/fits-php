# FitsPHP - A dumb FITS image library for PHP

FitsPHP (or `fits-php`) is a dumb and crappy "library" to somehow interact with the FITS image format used in astronomy.  
The library tries to implement a reasonable subset of the [FITS standard](https://fits.gsfc.nasa.gov/fits_standard.html). Of course, it all depends on the definition of 'reasonable'...

## Usage

Add the package with Composer:

```
composer require dumbastro/fits-php
```

then use classes from the `Dumbastro\FitsPhp` namespace.

### Examples

Retrieve the image blob for a given FITS file then do something with the bytes:

```php
<?php

declare(strict_types=1);

use Dumbastro\FitsPhp\Fits;
use Dumbastro\FitsPhp\FitsHeader;
use Dumbastro\FitsPhp\ImageBlob;

$fits = new Fits('bubble_nebula.fit');
$blob = new ImageBlob($header, $fits->imageBlob);

foreach ($blob->dataBytes() as $byte) {
    // Do something useful...
}
```

The method `ImageBlob::dataBytes` returns a `Generator`.


