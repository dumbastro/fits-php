<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

/**
* A FITS keyword record
* @todo Add types for max lengths?
*/
readonly class Keyword
{
    public function __construct(
        public string $name,
        public string $value,
        public ?string $comment,
    ) {}
}
