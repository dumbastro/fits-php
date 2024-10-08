<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

/**
* Valid BITPIX values
*/
enum Bitpix: int
{
    case Uint8 = 8;
    case Uint16 = 16;
    case Uint32 = 32;
    case Uint64 = 64;
    case Float32 = -32;
    case Float64 = -64;

    public function type(): string
    {
        return match($this) {
            Bitpix::Uint8 => 'int8',
            Bitpix::Uint16 => 'int16',
            Bitpix::Uint32 => 'int32',
            Bitpix::Uint64 => 'int64',
            Bitpix::Float32 => 'float32',
            Bitpix::Float64 => 'float64',
        };
    }

    public function toString(): string
    {
        return match($this) {
            Bitpix::Uint8 => 'Character or unsigned binary integer',
            Bitpix::Uint16 => '16 bit two\'s complement binary integer',
            Bitpix::Uint32 => '32 bit two\'s complement binary integer',
            Bitpix::Uint64 => '64 bit two\'s complement binary integer',
            Bitpix::Float32 => 'IEEE single-precision floating point',
            Bitpix::Float64 => 'IEEE double-precision floating point',
        };
    }
}

