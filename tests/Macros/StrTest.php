<?php

namespace Flavorly\LaravelHelpers\Tests\Macros;

use Flavorly\LaravelHelpers\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StrTest extends TestCase
{
    /** @test */
    public function it_can_convert_lines_to_collection()
    {
        // Test basic string conversion
        $result = Str::linesToCollection('apple,banana,cherry');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(['apple', 'banana', 'cherry'], $result->all());

        // Test with custom delimiter
        $result = Str::linesToCollection('apple|banana|cherry', '|');
        $this->assertEquals(['apple', 'banana', 'cherry'], $result->all());

        // Test with duplicates and unique=true (default)
        $result = Str::linesToCollection('apple,banana,apple,cherry');
        $this->assertEquals(['apple', 'banana', 'cherry'], $result->all());

        // Test with duplicates and unique=false
        $result = Str::linesToCollection('apple,banana,apple,cherry', ',', false);
        $this->assertEquals(['apple', 'banana', 'apple', 'cherry'], $result->all());

        // Test with spaces and trimming
        $result = Str::linesToCollection('  apple  ,  banana  , cherry  ');
        $this->assertEquals(['apple', 'banana', 'cherry'], $result->all());

        // Test with empty values
        $result = Str::linesToCollection('apple,,banana,,cherry');
        $this->assertEquals(['apple', 'banana', 'cherry'], $result->all());

        // Test with array input
        $result = Str::linesToCollection(['apple', 'banana', 'apple', 'cherry']);
        $this->assertEquals(['apple', 'banana', 'cherry'], $result->all());

        // Test with mixed case duplicates
        $result = Str::linesToCollection('Apple,apple,APPLE,banana');
        $this->assertEquals(['Apple', 'apple', 'APPLE', 'banana'], $result->all());
    }

    /** @test */
    public function it_handles_edge_cases_for_lines_to_collection()
    {
        // Empty string
        $result = Str::linesToCollection('');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals([], $result->all());

        // Single value
        $result = Str::linesToCollection('apple');
        $this->assertEquals(['apple'], $result->all());

        // Only delimiters
        $result = Str::linesToCollection(',,,');
        $this->assertEquals([], $result->all());

        // Only spaces
        $result = Str::linesToCollection('   ');
        $this->assertEquals([], $result->all());

        // Unicode characters
        $result = Str::linesToCollection('café,résumé,naïve');
        $this->assertEquals(['café', 'résumé', 'naïve'], $result->all());
    }
}
