<?php declare(strict_types=1);

namespace Analyzer;

use Exception;

class Analyzer
{
    /**
     * @var DataSource $dataSource
     */
    private $dataSource;

    /**
     * @var array $data
     */
    private $data;

    /**
     * @var array $chunks
     */
    private $chunks;

    /**
     * @var float $longestChunk
     */
    private $longestChunk;

    /**
     * @var float $totalDuration
     */
    private $totalDuration;

    /**
     * @var float $latestEntry
     */
    private $latestEntry;


    public function getChunks(): array
    {
        return $this->chunks;
    }

    public function getLatestEntry(): float
    {
        return $this->latestEntry;
    }

    public function getLongestChunk(): float
    {
        return $this->longestChunk;
    }

    public function getTotalDuration(): float
    {
        return $this->totalDuration;
    }

    public function __construct(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function loadData(): Analyzer
    {
        $this->data = $this->dataSource->getData();
        return $this;
    }

    public function findLatestEntry(): Analyzer
    {
        if (!preg_match('/silence_(start|end): ([\d\.]+)/', end($this->data), $matches)) {
            throw new Exception('Invalid data entry.');
        }
        $this->latestEntry = (float)$matches[2];
        return $this;
    }

    public function splitChunks(): Analyzer
    {
        $this->chunks = [];
        $chunkStart = 0;

        foreach ($this->data as $row) {
            if (preg_match('/silence_start: ([\d\.]+)/', $row, $matches)) {
                $this->chunks[] = [$chunkStart, (float)$matches[1]];
            } elseif (preg_match('/silence_end: ([\d\.]+)/', $row, $matches)) {
                $chunkStart = (float)$matches[1];
            } else {
                throw new Exception('Invalid data entry.');
            }
        }

        return $this;
    }

    public function findLongestChunk(): Analyzer
    {
        $this->longestChunk = 0;

        foreach ($this->chunks as $chunk) {
            $this->longestChunk = max($this->longestChunk, $chunk[1] - $chunk[0]);
        }

        return $this;
    }

    public function findTotalDuration(): Analyzer
    {
        $this->totalDuration = 0;

        foreach ($this->chunks as $chunk) {
            $this->totalDuration += $chunk[1] - $chunk[0];
        }

        return $this;
    }

    public function analyze(): Analyzer
    {
        return (new static($this->dataSource))
            ->loadData()
            ->findLatestEntry()
            ->splitChunks()
            ->findLongestChunk()
            ->findTotalDuration();
    }
}
