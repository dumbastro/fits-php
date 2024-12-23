<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FitsTest extends TestCase
{
    public function testValidatesFits(): void
    {
        $fits = new Dumbastro\FitsPhp\Fits(__DIR__ . '/test_orion.fit');

        $this->assertTrue($fits->validate());
    }
}
 
