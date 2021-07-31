<?php declare(strict_types=1);

namespace Tests;

use Analyzer\DataSource;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DataSourceTest extends TestCase
{
    public function testGetData()
    {
        $expected = ['1','2','3'];
        $actual = (new DataSource(__DIR__ . '/fixtures/data0.txt'))
                  ->getData();
        $this->assertSame($expected, $actual);
    }

    public function testMissingFile()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File "blah" does not exists.');
        new DataSource('blah');
    }
}
