<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Traits;

use Skpassegna\Json\Tests\TestCase;
use Skpassegna\Json\Traits\TransformationTrait;
use Skpassegna\Json\Exceptions\TransformException;

class TransformationTraitTest extends TestCase
{
    use TransformationTrait;

    private array $testData = [
        'user' => [
            'name' => 'John Doe',
            'age' => 30,
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'country' => 'USA'
            ],
            'hobbies' => ['reading', 'gaming']
        ]
    ];

    /**
     * @test
     */
    public function it_converts_to_xml(): void
    {
        $xml = $this->toXml($this->testData);

        $this->assertStringContainsString('<user>', $xml);
        $this->assertStringContainsString('<name>John Doe</name>', $xml);
        $this->assertStringContainsString('<age>30</age>', $xml);
        $this->assertStringContainsString('<street>123 Main St</street>', $xml);
        $this->assertStringContainsString('<hobbies>reading</hobbies>', $xml);
    }

    /**
     * @test
     */
    public function it_converts_from_xml(): void
    {
        $xml = $this->toXml($this->testData);
        $data = $this->fromXml($xml);

        $this->assertEquals($this->testData['user']['name'], $data['user']['name']);
        $this->assertEquals($this->testData['user']['age'], $data['user']['age']);
        $this->assertEquals($this->testData['user']['address'], $data['user']['address']);
    }

    /**
     * @test
     */
    public function it_converts_to_yaml(): void
    {
        $yaml = $this->toYaml($this->testData);

        $this->assertStringContainsString('user:', $yaml);
        $this->assertStringContainsString('  name: John Doe', $yaml);
        $this->assertStringContainsString('  age: 30', $yaml);
        $this->assertStringContainsString('  address:', $yaml);
        $this->assertStringContainsString('    street: 123 Main St', $yaml);
    }

    /**
     * @test
     */
    public function it_converts_from_yaml(): void
    {
        $yaml = $this->toYaml($this->testData);
        $data = $this->fromYaml($yaml);

        $this->assertEquals($this->testData, $data);
    }

    /**
     * @test
     */
    public function it_converts_to_csv(): void
    {
        $data = [
            ['name' => 'John', 'age' => 30, 'city' => 'New York'],
            ['name' => 'Jane', 'age' => 25, 'city' => 'Los Angeles']
        ];

        $csv = $this->toCsv($data);

        $this->assertStringContainsString('name,age,city', $csv);
        $this->assertStringContainsString('John,30,New York', $csv);
        $this->assertStringContainsString('Jane,25,Los Angeles', $csv);
    }

    /**
     * @test
     */
    public function it_converts_from_csv(): void
    {
        $data = [
            ['name' => 'John', 'age' => '30', 'city' => 'New York'],
            ['name' => 'Jane', 'age' => '25', 'city' => 'Los Angeles']
        ];

        $csv = $this->toCsv($data);
        $result = $this->fromCsv($csv);

        $this->assertEquals($data, $result);
    }

    /**
     * @test
     */
    public function it_converts_html_to_json(): void
    {
        $html = '
            <div class="user-profile">
                <h1>John Doe</h1>
                <div class="details">
                    <p>Age: 30</p>
                    <p>Location: New York</p>
                </div>
                <ul class="hobbies">
                    <li>Reading</li>
                    <li>Gaming</li>
                </ul>
            </div>
        ';

        $result = $this->fromHtml($html);

        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('div', $result['name']);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertEquals('user-profile', $result['attributes']['class']);
    }

    /**
     * @test
     */
    public function it_handles_html_tables(): void
    {
        $html = '
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John</td>
                        <td>30</td>
                    </tr>
                    <tr>
                        <td>Jane</td>
                        <td>25</td>
                    </tr>
                </tbody>
            </table>
        ';

        $result = $this->fromHtml($html, ['parseTable' => true]);

        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('rows', $result);
        $this->assertEquals(['Name', 'Age'], $result['headers']);
        $this->assertCount(2, $result['rows']);
    }

    /**
     * @test
     */
    public function it_flattens_nested_array(): void
    {
        $flattened = $this->flatten($this->testData);

        $this->assertEquals('John Doe', $flattened['user.name']);
        $this->assertEquals(30, $flattened['user.age']);
        $this->assertEquals('123 Main St', $flattened['user.address.street']);
        $this->assertEquals('reading', $flattened['user.hobbies.0']);
    }

    /**
     * @test
     */
    public function it_unflattens_array(): void
    {
        $flat = [
            'user.name' => 'John Doe',
            'user.age' => 30,
            'user.address.street' => '123 Main St',
            'user.hobbies.0' => 'reading'
        ];

        $unflattened = $this->unflatten($flat);
        
        $this->assertEquals('John Doe', $unflattened['user']['name']);
        $this->assertEquals(30, $unflattened['user']['age']);
        $this->assertEquals('123 Main St', $unflattened['user']['address']['street']);
        $this->assertEquals('reading', $unflattened['user']['hobbies'][0]);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_xml(): void
    {
        $this->expectException(TransformException::class);
        $this->fromXml('<invalid>xml');
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_yaml(): void
    {
        $this->expectException(TransformException::class);
        $this->fromYaml('invalid: yaml: : :');
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_csv(): void
    {
        $this->expectException(TransformException::class);
        $this->fromCsv("name,age\ninvalid,csv,extra");
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_html(): void
    {
        $this->expectException(TransformException::class);
        $this->fromHtml('<div>unclosed');
    }
}
