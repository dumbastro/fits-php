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
    * @todo Comments and keyword values could span more
            than one 80-bytes block...
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
            $comment = $splitByComment[1] ?? null;
            $keyVal = explode('=', $splitByComment[0]);
            $name = $keyVal[0];
            $value = $keyVal[1] ?? '';

            if (str_starts_with($name, 'COMMENT')) {
                $value = explode('COMMENT', $name)[1];
                $name = 'COMMENT';
            }

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
    * Retrieve a Keyword object based on key name
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

