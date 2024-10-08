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
    /**
    * @var string[] $blanks
    */
    private array $blanks;

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

        $filtered = array_filter(
            $records,
            fn (string $r) => trim($r) !== '' && !str_starts_with($r, 'END')
        );

        $this->blanks = array_diff($records, $filtered);

        $keywords = [];

        foreach ($filtered as $record) {
            $splitByComment = explode('/', $record);
            $comment = isset($splitByComment[1]) ? $splitByComment[1] : null;
            [$name, $value] = explode('=', $splitByComment[0]);

            $keywords[] = new Keyword(
                name : $name,
                value : $value,
                comment : $comment,
            );
        }

        return $keywords;
    }
    /**
    * Return the FITS header as a string (byte stream)
    */
    public function toString(): string
    {
        $blanks = implode('', $this->blanks);
        $keywordsString = '';

        foreach ($this->keywords as $keyword) {
            $keywordsString .= $keyword->toString();
        }

        return $keywordsString . $blanks;
    }
    /**
    * Retrieve a Keyword object base on key name
    */
    public function keyword(string $key): ?Keyword
    {
        $keyword = null;

        foreach ($this->keywords as $k) {
            if (trim($k->name) === $key) {
                $keyword = $k;
                break;
            }
        }

        return $keyword;
    }
    /**
    * Note: the keyword key string is case-sensitive
    */
    public function getKeywordValue(string $key): string
    {
        $value = '';
        foreach ($this->keywords as $keyword) {
            if (trim($keyword->name) === $key) {
                $value = $keyword->value;
                break;
            }
        }

        return $value;
    }
}

