<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp\Exceptions;

class InvalidBitpixValue extends \Exception
{
    public function __construct(int $bitpix)
    {
        $this->message = "The value $bitpix is not a valid BITPIX value";
    }
}
