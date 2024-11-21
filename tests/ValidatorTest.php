<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json\Validator;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testIsValidWithValidJson(): void
    {
        $json = '{"name":"John","age":30}';
        $this->assertTrue($this->validator->isValid($json));
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testIsValidWithInvalidJson(): void
    {
        $json = '{"name":"John",age:30}'; // Missing quotes around age
        $this->assertFalse($this->validator->isValid($json));
        $this->assertNotEmpty($this->validator->getErrors());
    }

    public function testValidateSchemaWithValidData(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "name": {"type": "string"},
                "age": {"type": "integer"}
            },
            "required": ["name", "age"]
        }';
        
        $data = '{"name":"John","age":30}';
        
        $this->assertTrue($this->validator->validateSchema($data, $schema));
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testValidateSchemaWithInvalidData(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "name": {"type": "string"},
                "age": {"type": "integer"}
            },
            "required": ["name", "age"]
        }';
        
        $data = '{"name":"John","age":"thirty"}'; // age should be integer
        
        $this->assertFalse($this->validator->validateSchema($data, $schema));
        $this->assertNotEmpty($this->validator->getErrors());
    }

    public function testValidateSchemaWithInvalidSchema(): void
    {
        $this->expectException(\Skpassegna\Json\Exceptions\InvalidArgumentException::class);
        
        $schema = '{invalid schema}';
        $data = '{"name":"John","age":30}';
        
        $this->validator->validateSchema($data, $schema);
    }
}
