<?php declare(strict_types=1);

namespace Tests;

use Analyzer\Analyzer;
use Analyzer\DataSource;
use Exception;
use PHPUnit\Framework\TestCase;
use Mockery;

final class AnalyzerTest extends TestCase
{
    /**
     * @var DataSource|Mockery\MockInterface
     */
    private $dataSource;

    public function setUp()
    {
        parent::setUp();

        /** @var DataSource|Mockery\MockInterface */
        $this->dataSource = Mockery::mock(DataSource::class);
    }

    public function tearDown()
    {
        $this->dataSource = null;

        parent::tearDown();
    }

    public function testLoadData()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn([]);

        $this->assertInstanceOf(Analyzer::class, (new Analyzer($this->dataSource))->loadData());
    }

    public function testFindLatestEntry()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn(['[silencedetect @ 0x7fbfbbc076a0] silence_end: 123.456 | silence_duration: 2.512']);

        $actual = (new Analyzer($this->dataSource))
                ->loadData()
                ->findLatestEntry()
                ->getLatestEntry();

        $this->assertSame(123.456, $actual);
    }

    public function testFindLatestEntryException()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn(['blah']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid data entry.');

        (new Analyzer($this->dataSource))->loadData()->findLatestEntry();
    }

    public function testSplitChunks()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn([
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 4',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 6 | silence_duration: 3.152',
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 10',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 12 | silence_duration: 5.712',
            ]);

        $actual = (new Analyzer($this->dataSource))
            ->loadData()
            ->splitChunks()
            ->getChunks();

        $expected = [
            [0, 4],
            [6, 10]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSplitChunksException()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn([
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 4',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 6 | silence_duration: 3.152',
                '[silencedetect @ 0x7fbfbbc076a0] silence_blah: 10',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 12 | silence_duration: 5.712',
            ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid data entry.');

        (new Analyzer($this->dataSource))->loadData()->splitChunks();
    }
    public function testFindLongestChunk()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn([
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 5',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 6 | silence_duration: 3.152',
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 9',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 12 | silence_duration: 5.712',
            ]);

        $actual = (new Analyzer($this->dataSource))
            ->loadData()
            ->splitChunks()
            ->findLongestChunk()
            ->getLongestChunk();

        $this->assertSame(5.0, $actual);
    }

    public function testFindTotalDuration()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn([
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 5',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 6 | silence_duration: 3.152',
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 9',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 12 | silence_duration: 5.712',
            ]);

        $actual = (new Analyzer($this->dataSource))
            ->loadData()
            ->splitChunks()
            ->findTotalDuration()
            ->getTotalDuration();

        $this->assertSame(8.0, $actual);
    }

    public function testAnalyze()
    {
        $this->dataSource->shouldReceive('getData')
            ->andReturn([
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 5',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 6 | silence_duration: 3.152',
                '[silencedetect @ 0x7fbfbbc076a0] silence_start: 9',
                '[silencedetect @ 0x7fbfbbc076a0] silence_end: 12 | silence_duration: 5.712',
            ]);

        $this->assertInstanceOf(Analyzer::class, (new Analyzer($this->dataSource))->analyze());
    }
}
