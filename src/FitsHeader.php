<?php

declare(strict_types=1);

namespace Dumbastro\FitsPhp;

use Dumbastro\FitsPhp\Keyword;

class FitsHeader
{
    private string $headerBlock;
    /**
    * @var Keyword[] $keywords
    */
    public readonly array $keywords;

    public function __construct(string $headerBlock)
    {
        $this->headerBlock = $headerBlock;
        $this->keywords = $this->readKeywords();
    }
    /**
    * Initialize the keyword records array
    * 
    * From the spec: each keyword record, including
    * any comments, is at most 80 bytes long
    * @return Keyword[]
    */
    private function readKeywords(): array
    {
        $records = str_split($this->headerBlock, 80);

        $records = array_filter(
            $records,
            fn (string $r) => trim($r) !== '' && !str_starts_with($r, 'END')
        );

        $keywords = [];

        foreach ($records as $record) {
            $splitByComment = explode('/', $record);
            $comment = isset($splitByComment[1]) ? trim($splitByComment[1]) : null;
            $nameValue = explode('=', $splitByComment[0]);

            $keywords[] = new Keyword(
                name : trim($nameValue[0]),
                value : trim($nameValue[1]),
                comment : $comment,
            );
        }

        return $keywords;
    }
}

