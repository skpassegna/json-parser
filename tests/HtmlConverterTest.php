<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json\HtmlConverter;

class HtmlConverterTest extends TestCase
{
    private HtmlConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new HtmlConverter();
    }

    public function testBasicHtmlToJson(): void
    {
        $html = '<div class="container"><p>Hello World</p></div>';
        $json = $this->converter->convert($html);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertEquals('div', $data['_type']);
        $this->assertEquals(['class' => 'container'], $data['_attributes']);
        $this->assertCount(1, $data['_children']);
        $this->assertEquals('p', $data['_children'][0]['_type']);
        $this->assertEquals('Hello World', $data['_children'][0]['_children'][0]['_text']);
    }

    public function testSimplifiedFormat(): void
    {
        $html = '<div><p>Hello</p><span>World</span></div>';
        $json = $this->converter->convert($html, ['format' => 'simplified']);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('p', $data);
        $this->assertEquals('Hello', $data['p']['_text']);
        $this->assertArrayHasKey('span', $data);
        $this->assertEquals('World', $data['span']['_text']);
    }

    public function testFlatFormat(): void
    {
        $html = '<div><p>Hello</p><span>World</span></div>';
        $json = $this->converter->convert($html, ['format' => 'flat']);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertCount(3, $data); // div, p, span
        $this->assertEquals('div', $data[0]['_type']);
        $this->assertEquals('p', $data[1]['_type']);
        $this->assertEquals('span', $data[2]['_type']);
    }

    public function testCustomAttributes(): void
    {
        $html = '<div data-custom="test" class="container">Content</div>';
        $json = $this->converter->convert($html, [
            'customAttributes' => ['data-custom']
        ]);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('_attributes', $data);
        $this->assertEquals('test', $data['_attributes']['data-custom']);
        $this->assertEquals('container', $data['_attributes']['class']);
    }

    public function testExcludeTags(): void
    {
        $html = '<div><script>alert(1);</script><p>Content</p></div>';
        $json = $this->converter->convert($html, [
            'excludeTags' => ['script']
        ]);
        $data = json_decode($json, true);

        $this->assertCount(1, $data['_children']);
        $this->assertEquals('p', $data['_children'][0]['_type']);
    }

    public function testPreserveWhitespace(): void
    {
        $html = '<pre>  Formatted  Text  </pre>';
        $json = $this->converter->convert($html, [
            'preserveWhitespace' => true
        ]);
        $data = json_decode($json, true);

        $this->assertEquals('  Formatted  Text  ', $data['_children'][0]['_text']);
    }

    public function testSelfClosingTags(): void
    {
        $html = '<div><img src="test.jpg" alt="Test"><br></div>';
        $json = $this->converter->convert($html);
        $data = json_decode($json, true);

        $this->assertCount(2, $data['_children']);
        $this->assertEquals('img', $data['_children'][0]['_type']);
        $this->assertEquals('br', $data['_children'][1]['_type']);
    }

    public function testJsonToHtml(): void
    {
        $json = json_encode([
            '_type' => 'div',
            '_attributes' => ['class' => 'container'],
            '_children' => [
                [
                    '_type' => 'p',
                    '_children' => [
                        ['_text' => 'Hello World']
                    ]
                ]
            ]
        ]);

        $html = $this->converter->toHtml($json);
        $expected = '<div class="container"><p>Hello World</p></div>';
        
        // Normalize HTML by removing whitespace
        $normalizedHtml = preg_replace('/\s+/', '', $html);
        $normalizedExpected = preg_replace('/\s+/', '', $expected);
        
        $this->assertEquals($normalizedExpected, $normalizedHtml);
    }

    public function testComplexNestedStructure(): void
    {
        $html = '
            <article class="post">
                <header>
                    <h1>Title</h1>
                    <div class="meta">
                        <span class="date">2023-01-01</span>
                        <span class="author">John Doe</span>
                    </div>
                </header>
                <div class="content">
                    <p>First paragraph</p>
                    <p>Second paragraph</p>
                </div>
            </article>
        ';

        $json = $this->converter->convert($html);
        $data = json_decode($json, true);

        $this->assertEquals('article', $data['_type']);
        $this->assertEquals('post', $data['_attributes']['class']);
        $this->assertCount(2, $data['_children']); // header and div.content

        // Test round-trip conversion
        $backToHtml = $this->converter->toHtml($json);
        $normalizedOriginal = preg_replace('/\s+/', '', $html);
        $normalizedResult = preg_replace('/\s+/', '', $backToHtml);
        
        $this->assertEquals($normalizedOriginal, $normalizedResult);
    }

    public function testInvalidHtml(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->converter->convert('<div>Unclosed');
    }

    public function testInvalidJson(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->converter->toHtml('invalid json');
    }
}
