<?php

declare(strict_types=1);

namespace Skpassegna\Json\Benchmarks;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Skpassegna\Json\Json\Serializer;
use Skpassegna\Json\Json\Validator;
use Skpassegna\Json\Json\Transformer;

/**
 * @BeforeMethods({"init"})
 * @Warmup(2)
 * @OutputTimeUnit("milliseconds", precision=3)
 */
class JsonBench
{
    private Serializer $serializer;
    private Validator $validator;
    private Transformer $transformer;
    private string $smallJson;
    private string $largeJson;
    private string $schema;

    public function init(): void
    {
        $this->serializer = new Serializer();
        $this->validator = new Validator();
        $this->transformer = new Transformer();

        // Small JSON dataset
        $this->smallJson = json_encode([
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'john@example.com',
            'hobbies' => ['reading', 'gaming', 'coding'],
            'address' => [
                'street' => '123 Main St',
                'city' => 'Boston',
                'country' => 'USA'
            ]
        ]);

        // Large JSON dataset (1000 records)
        $largeData = [];
        for ($i = 0; $i < 1000; $i++) {
            $largeData[] = [
                'id' => $i,
                'name' => "User $i",
                'email' => "user$i@example.com",
                'age' => rand(18, 80),
                'active' => (bool) rand(0, 1),
                'created_at' => date('Y-m-d H:i:s'),
                'metadata' => [
                    'ip' => long2ip(rand(0, 4294967295)),
                    'user_agent' => 'Mozilla/5.0',
                    'last_login' => date('Y-m-d H:i:s'),
                    'preferences' => [
                        'theme' => rand(0, 1) ? 'light' : 'dark',
                        'notifications' => (bool) rand(0, 1),
                        'language' => ['en', 'fr', 'de'][rand(0, 2)]
                    ]
                ]
            ];
        }
        $this->largeJson = json_encode($largeData);

        // JSON Schema for validation
        $this->schema = json_encode([
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
                'email' => ['type' => 'string', 'format' => 'email'],
                'hobbies' => [
                    'type' => 'array',
                    'items' => ['type' => 'string']
                ],
                'address' => [
                    'type' => 'object',
                    'properties' => [
                        'street' => ['type' => 'string'],
                        'city' => ['type' => 'string'],
                        'country' => ['type' => 'string']
                    ]
                ]
            ]
        ]);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSerializeSmall(): void
    {
        $data = json_decode($this->smallJson, true);
        $this->serializer->serialize($data);
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchSerializeLarge(): void
    {
        $data = json_decode($this->largeJson, true);
        $this->serializer->serialize($data);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchDeserializeSmall(): void
    {
        $this->serializer->deserialize($this->smallJson, true);
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchDeserializeLarge(): void
    {
        $this->serializer->deserialize($this->largeJson, true);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchValidateSmall(): void
    {
        $this->validator->isValid($this->smallJson);
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchValidateLarge(): void
    {
        $this->validator->isValid($this->largeJson);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSchemaValidation(): void
    {
        $this->validator->validateSchema($this->smallJson, $this->schema);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchToXml(): void
    {
        $this->transformer->toXml($this->smallJson);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchToCsv(): void
    {
        $json = '[{"name":"John","age":30},{"name":"Jane","age":25}]';
        $this->transformer->toCsv($json);
    }
}
