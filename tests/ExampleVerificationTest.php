<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;

class ExampleVerificationTest extends TestCase
{
    /**
     * Test that procedural/basic.php example runs without errors.
     */
    public function test_procedural_basic_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/procedural/basic.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('Procedural API - Basic Usage', $output);
        self::assertStringContainsString('completed successfully', $output);
    }

    /**
     * Test that procedural/merge-diff.php example runs without errors.
     */
    public function test_procedural_merge_diff_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/procedural/merge-diff.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('Procedural API - Merge and Diff', $output);
        self::assertStringContainsString('completed successfully', $output);
    }

    /**
     * Test that coercion/type-conversion.php example runs without errors.
     */
    public function test_coercion_type_conversion_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/coercion/type-conversion.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('Type Coercion Examples', $output);
        self::assertStringContainsString('completed', $output);
    }

    /**
     * Test that events/dispatcher-usage.php example runs without errors.
     */
    public function test_events_dispatcher_usage_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/events/dispatcher-usage.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('Event System and Dispatcher', $output);
        self::assertStringContainsString('completed', $output);
    }

    /**
     * Test that security/input-validation.php example runs without errors.
     */
    public function test_security_input_validation_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/security/input-validation.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('Security - Input Validation', $output);
        self::assertStringContainsString('completed', $output);
    }

    /**
     * Test that performance/caching-optimization.php example runs without errors.
     */
    public function test_performance_caching_optimization_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/performance/caching-optimization.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('Performance - Caching and Optimization', $output);
        self::assertStringContainsString('completed', $output);
    }

    /**
     * Test that streaming/basic-streaming.php example runs without errors.
     */
    public function test_streaming_basic_example_runs(): void
    {
        ob_start();
        try {
            require __DIR__ . '/../examples/streaming/basic-streaming.php';
            $output = ob_get_clean();
        } catch (\Exception $e) {
            ob_get_clean();
            self::fail('Example failed: ' . $e->getMessage());
        }

        self::assertStringContainsString('streaming', strtolower($output));
    }

    /**
     * Test that all example files are executable and valid PHP.
     */
    public function test_all_example_files_are_valid_php(): void
    {
        $examplesDir = __DIR__ . '/../examples';
        $iterator = new \RecursiveDirectoryIterator($examplesDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $recursive = new \RecursiveIteratorIterator($iterator);
        
        foreach ($recursive as $file) {
            if ($file->getExtension() === 'php' && $file->getBasename() !== 'README.md') {
                $filePath = $file->getRealPath();
                
                $output = shell_exec("php -l \"$filePath\" 2>&1");
                self::assertStringContainsString('No syntax errors', $output, "Syntax error in $filePath: $output");
            }
        }
    }

    /**
     * Test that examples directory exists and has the expected structure.
     */
    public function test_examples_directory_structure(): void
    {
        self::assertDirectoryExists(__DIR__ . '/../examples');
        self::assertDirectoryExists(__DIR__ . '/../examples/procedural');
        self::assertDirectoryExists(__DIR__ . '/../examples/coercion');
        self::assertDirectoryExists(__DIR__ . '/../examples/events');
        self::assertDirectoryExists(__DIR__ . '/../examples/security');
        self::assertDirectoryExists(__DIR__ . '/../examples/performance');
        self::assertDirectoryExists(__DIR__ . '/../examples/streaming');
    }

    /**
     * Test that examples README exists and contains expected sections.
     */
    public function test_examples_readme_exists_and_complete(): void
    {
        $readmePath = __DIR__ . '/../examples/README.md';
        self::assertFileExists($readmePath);
        
        $content = file_get_contents($readmePath);
        self::assertStringContainsString('Procedural API', $content);
        self::assertStringContainsString('Security', $content);
        self::assertStringContainsString('Streaming', $content);
        self::assertStringContainsString('php examples/', $content);
    }
}
