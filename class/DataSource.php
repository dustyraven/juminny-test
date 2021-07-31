<?php declare(strict_types=1);

namespace Analyzer;

use InvalidArgumentException;

class DataSource
{
    /**
     * @var string $filename
     */
    private $filename;

    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException('File "' . $filename . '" does not exists.');
        }
        $this->filename = $filename;
    }

    public function getData(): array
    {
        return file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
