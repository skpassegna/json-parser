<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Utils;

use Skpassegna\Json\Tests\TestCase;
use Skpassegna\Json\Utils\HtmlConverter;
use Skpassegna\Json\Exceptions\TransformException;

class HtmlConverterTest extends TestCase
{
    /**
     * @test
     * @dataProvider basicHtmlProvider
     */
    public function it_converts_basic_html_to_array(string $html, array $expected): void
    {
        $result = HtmlConverter::toArray($html);
        $this->assertEquals($expected, $result);
    }

    public function basicHtmlProvider(): array
    {
        return [
            'simple_div' => [
                '<div>Hello</div>',
                [
                    'type' => 'element',
                    'name' => 'div',
                    'text' => 'Hello'
                ]
            ],
            'nested_elements' => [
                '<div><p>Text</p></div>',
                [
                    'type' => 'element',
                    'name' => 'div',
                    'children' => [
                        [
                            'type' => 'element',
                            'name' => 'p',
                            'text' => 'Text'
                        ]
                    ]
                ]
            ],
            'with_attributes' => [
                '<div class="container" id="main">Content</div>',
                [
                    'type' => 'element',
                    'name' => 'div',
                    'attributes' => [
                        'class' => 'container',
                        'id' => 'main'
                    ],
                    'text' => 'Content'
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function it_handles_excluded_tags(): void
    {
        $html = '<div><script>alert("test");</script><p>Content</p></div>';
        $result = HtmlConverter::toArray($html, ['excludeTags' => ['script']]);

        $this->assertEquals([
            'type' => 'element',
            'name' => 'div',
            'children' => [
                [
                    'type' => 'element',
                    'name' => 'p',
                    'text' => 'Content'
                ]
            ]
        ], $result);
    }

    /**
     * @test
     */
    public function it_preserves_whitespace_when_configured(): void
    {
        $html = '<div>  Spaced  Content  </div>';
        
        $withoutPreserve = HtmlConverter::toArray($html);
        $withPreserve = HtmlConverter::toArray($html, ['preserveWhitespace' => true]);

        $this->assertEquals('Spaced Content', $withoutPreserve['text']);
        $this->assertEquals('  Spaced  Content  ', $withPreserve['text']);
    }

    /**
     * @test
     */
    public function it_converts_tables_to_structured_array(): void
    {
        $html = '
            <table>
                <thead>
                    <tr><th>Name</th><th>Age</th></tr>
                </thead>
                <tbody>
                    <tr><td>John</td><td>30</td></tr>
                    <tr><td>Jane</td><td>25</td></tr>
                </tbody>
            </table>
        ';

        $result = HtmlConverter::tableToArray(new \Symfony\Component\DomCrawler\Crawler($html));

        $expected = [
            'headers' => ['Name', 'Age'],
            'rows' => [
                ['Name' => 'John', 'Age' => '30'],
                ['Name' => 'Jane', 'Age' => '25']
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_handles_custom_tag_transformations(): void
    {
        $html = '<div><custom-tag>Special Content</custom-tag></div>';
        
        $transformer = function(\Symfony\Component\DomCrawler\Crawler $node) {
            return [
                'type' => 'custom',
                'content' => $node->text()
            ];
        };

        $result = HtmlConverter::toArray($html, [
            'transformTags' => ['custom-tag' => $transformer]
        ]);

        $this->assertEquals([
            'type' => 'element',
            'name' => 'div',
            'children' => [
                [
                    'type' => 'custom',
                    'content' => 'Special Content'
                ]
            ]
        ], $result);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_html(): void
    {
        $this->expectException(TransformException::class);
        HtmlConverter::toArray('<div>Unclosed');
    }

    /**
     * @test
     */
    public function it_handles_empty_elements(): void
    {
        $result = HtmlConverter::toArray('<div></div>');
        $this->assertEquals([
            'type' => 'element',
            'name' => 'div'
        ], $result);
    }

    /**
     * @test
     */
    public function it_handles_self_closing_tags(): void
    {
        $result = HtmlConverter::toArray('<img src="test.jpg" />');
        $this->assertEquals([
            'type' => 'element',
            'name' => 'img',
            'attributes' => [
                'src' => 'test.jpg'
            ]
        ], $result);
    }
}
