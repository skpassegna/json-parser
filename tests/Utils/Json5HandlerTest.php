<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Utils;

use Skpassegna\Json\Tests\TestCase;
use Skpassegna\Json\Utils\Json5Handler;
use Skpassegna\Json\Exceptions\TransformException;

class Json5HandlerTest extends TestCase
{
    private Json5Handler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new Json5Handler();
    }

    /**
     * @test
     * @dataProvider validJson5Provider
     */
    public function it_parses_valid_json5(string $input, array $expected): void
    {
        $result = $this->handler->parse($input);
        $this->assertEquals($expected, $result);
    }

    public function validJson5Provider(): array
    {
        return [
            'basic_json5' => [
                '{
                    // Single line comment
                    name: "John",
                    /* Multi-line
                       comment */
                    age: 30,
                }',
                [
                    'name' => 'John',
                    'age' => 30
                ]
            ],
            'with_trailing_commas' => [
                '{
                    items: [
                        1,
                        2,
                        3,
                    ],
                }',
                [
                    'items' => [1, 2, 3]
                ]
            ],
            'with_hex_numbers' => [
                '{
                    color: 0xFF0000,
                    opacity: .5
                }',
                [
                    'color' => 0xFF0000,
                    'opacity' => 0.5
                ]
            ],
            'with_unquoted_keys' => [
                '{
                    unquoted: "value",
                    "quoted": "value2"
                }',
                [
                    'unquoted' => 'value',
                    'quoted' => 'value2'
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function it_extracts_comments(): void
    {
        $json5 = '{
            // Header comment
            name: "John", // Inline comment
            /* Block comment
               Multiple lines */
            age: 30
        }';

        $comments = $this->handler->extractComments($json5);
        
        $this->assertCount(3, $comments);
        $this->assertStringContainsString('Header comment', $comments[0]);
        $this->assertStringContainsString('Inline comment', $comments[1]);
        $this->assertStringContainsString('Block comment', $comments[2]);
    }

    /**
     * @test
     */
    public function it_preserves_comments_when_configured(): void
    {
        $json5 = '{
            // Header
            name: "John", // Name comment
            age: 30 /* Age */
        }';

        $result = $this->handler->parse($json5, ['preserveComments' => true]);
        
        $this->assertArrayHasKey('__comments', $result);
        $this->assertCount(3, $result['__comments']);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_json5(): void
    {
        $this->expectException(TransformException::class);
        $this->handler->parse('{invalid: json5');
    }

    /**
     * @test
     */
    public function it_handles_special_json5_features(): void
    {
        $json5 = '{
            // Special features
            infinity: Infinity,
            negInfinity: -Infinity,
            nan: NaN,
            undefined: undefined,
            hex: 0xDEADbeef,
            binary: 0b1010,
            octal: 0o744
        }';

        $result = $this->handler->parse($json5);

        $this->assertEquals(INF, $result['infinity']);
        $this->assertEquals(-INF, $result['negInfinity']);
        $this->assertTrue(is_nan($result['nan']));
        $this->assertNull($result['undefined']);
        $this->assertEquals(0xDEADbeef, $result['hex']);
        $this->assertEquals(0b1010, $result['binary']);
        $this->assertEquals(0o744, $result['octal']);
    }

    /**
     * @test
     */
    public function it_handles_multiline_strings(): void
    {
        $json5 = '{
            multiline: "Line 1 \
                       Line 2 \
                       Line 3"
        }';

        $result = $this->handler->parse($json5);
        $expected = "Line 1 Line 2 Line 3";
        
        $this->assertEquals($expected, $result['multiline']);
    }

    /**
     * @test
     */
    public function it_handles_single_quoted_strings(): void
    {
        $json5 = "{
            single: 'single quoted',
            mixed: 'can\\'t'
        }";

        $result = $this->handler->parse($json5);
        
        $this->assertEquals('single quoted', $result['single']);
        $this->assertEquals("can't", $result['mixed']);
    }
}
