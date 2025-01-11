<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

/**
* RGB image channels
*/
enum Channel
{
    case R;
    case G;
    case B;
    case L; // Luminance?
    case M; // Mono...
}
