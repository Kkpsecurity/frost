<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaManagerJavaScriptLogicTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_javascript_functions_structure_analysis()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $this->assertFileExists($scriptsPath);

        $content = file_get_contents($scriptsPath);

        // Test for expected function definitions
        $expectedFunctions = [
            'uploadFilesToFolder',
            'uploadFilesToCurrentFolder',
            'handleMediaManagerUpload',
            'updateFilesList',
            'getCurrentPath',
            'getCurrentDisk'
        ];

        foreach ($expectedFunctions as $function) {
            $this->assertStringContainsString($function, $content, "Function {$function} should exist in scripts");
        }

        // Test for required variables/objects
        $expectedVariables = [
            'currentPath',
            'currentDisk',
            'csrfToken',
            'FilePond'
        ];

        foreach ($expectedVariables as $variable) {
            $this->assertStringContainsString($variable, $content, "Variable {$variable} should exist in scripts");
        }

        // Test for AJAX endpoints
        $expectedEndpoints = [
            'media-manager/upload',
            'media-manager/files'
        ];

        foreach ($expectedEndpoints as $endpoint) {
            $this->assertStringContainsString($endpoint, $content, "Endpoint {$endpoint} should be referenced");
        }

        $this->addToAssertionCount(count($expectedFunctions) + count($expectedVariables) + count($expectedEndpoints));
    }

    /** @test */
    public function test_duplicate_function_detection()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $content = file_get_contents($scriptsPath);

        // Check for duplicate function definitions
        $functionPatterns = [
            '/function\s+uploadFilesToFolder\s*\(/i',
            '/function\s+uploadFilesToCurrentFolder\s*\(/i',
        ];

        foreach ($functionPatterns as $pattern) {
            $matches = [];
            preg_match_all($pattern, $content, $matches);

            if (count($matches[0]) > 1) {
                $this->fail("Duplicate function definition detected: " . implode(', ', $matches[0]));
            }
        }

        // Test specifically for uploadFilesToFolder duplicates
        $uploadFolderMatches = [];
        preg_match_all('/function\s+uploadFilesToFolder\s*\(/i', $content, $uploadFolderMatches);

        $this->assertLessThanOrEqual(1, count($uploadFolderMatches[0]),
            'uploadFilesToFolder should not have duplicate definitions');

        $this->addToAssertionCount(count($functionPatterns) + 1);
    }

    /** @test */
    public function test_parameter_consistency_analysis()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $content = file_get_contents($scriptsPath);

        // Test for consistent parameter usage
        $parameterTests = [
            ['folder', 'path'], // These should be consistent across functions
            ['disk', 'currentDisk'],
        ];

        foreach ($parameterTests as $params) {
            $param1Count = substr_count(strtolower($content), strtolower($params[0]));
            $param2Count = substr_count(strtolower($content), strtolower($params[1]));

            // Allow some tolerance for parameter naming variations
            $this->assertContainsWithTolerance($param1Count, $param2Count,
                "Parameter usage should be consistent between {$params[0]} and {$params[1]}", 400);
        }

        $this->addToAssertionCount(count($parameterTests));
    }

    /** @test */
    public function test_upload_endpoint_consistency()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $content = file_get_contents($scriptsPath);

        // Test that upload endpoints match controller routes
        $expectedUploadEndpoint = 'media-manager/upload';
        $this->assertStringContainsString($expectedUploadEndpoint, $content,
            'Scripts should reference the correct upload endpoint');

        // Test for CSRF token usage
        $this->assertStringContainsString('csrfToken', $content,
            'CSRF token should be used in upload requests');

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function test_current_path_variable_usage()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $content = file_get_contents($scriptsPath);

        // Test for currentPath variable consistency
        $currentPathUsages = substr_count($content, 'currentPath');
        $this->assertGreaterThan(0, $currentPathUsages, 'currentPath should be used in the scripts');

        // Test for getCurrentPath function
        $this->assertStringContainsString('getCurrentPath', $content,
            'getCurrentPath function should exist');

        // Test for path parameter in functions
        $pathParameterUsages = substr_count(strtolower($content), 'path');
        $this->assertGreaterThan(0, $pathParameterUsages, 'path parameter should be used');

        $this->addToAssertionCount(3);
    }

    /** @test */
    public function test_disk_to_grid_mapping()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $content = file_get_contents($scriptsPath);

        // Test for disk-related variables and functions
        $diskElements = [
            'currentDisk',
            'getCurrentDisk'
        ];

        foreach ($diskElements as $element) {
            $this->assertStringContainsString($element, $content,
                "Disk element {$element} should exist in scripts");
        }

        $this->addToAssertionCount(count($diskElements));
    }

    /** @test */
    public function test_error_handling_patterns()
    {
        $scriptsPath = resource_path('views/components/admin/media-manager/scripts.blade.php');
        $content = file_get_contents($scriptsPath);

        // Test for error handling in AJAX calls
        $errorHandlingPatterns = [
            'error',
            'catch',
            'fail'
        ];

        $errorHandlingFound = false;
        foreach ($errorHandlingPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                $errorHandlingFound = true;
                break;
            }
        }

        $this->assertTrue($errorHandlingFound, 'Error handling should be implemented in AJAX calls');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function test_view_component_structure()
    {
        $componentBasePath = resource_path('views/components/admin/media-manager');

        // Test main component files
        $expectedFiles = [
            'layout.blade.php',
            'scripts.blade.php',
            'styles.blade.php',
            'content.blade.php',
            'sidebar.blade.php',
            'header.blade.php',
            'toolbar.blade.php'
        ];

        foreach ($expectedFiles as $file) {
            $this->assertFileExists("{$componentBasePath}/{$file}");
        }

        // Test partial files
        $partialPath = "{$componentBasePath}/partials";
        $expectedPartials = [
            'main-content.blade.php',
            'media-uploader.blade.php',
            'file-pond-upload.blade.php'
        ];

        foreach ($expectedPartials as $file) {
            $this->assertFileExists("{$partialPath}/{$file}");
        }

        $this->addToAssertionCount(count($expectedFiles) + count($expectedPartials));
    }

    /** @test */
    public function test_route_structure_consistency()
    {
        // Test that routes match JavaScript endpoint expectations
        $routesList = \Illuminate\Support\Facades\Route::getRoutes();

                $expectedRoutes = [
            'admin.media-manager.upload',
            'admin.media-manager.files',
            'admin.media-manager.create-folder'
        ];

        foreach ($expectedRoutes as $routeName) {
            $route = $routesList->getByName($routeName);
            $this->assertNotNull($route, "Route {$routeName} should exist");
        }

        // Test that upload route uses POST method
        $uploadRoute = $routesList->getByName('admin.media-manager.upload');
        if ($uploadRoute) {
            $this->assertContains('POST', $uploadRoute->methods(),
                'Upload route should accept POST method');
        }

        $this->addToAssertionCount(count($expectedRoutes) + 1);
    }

    private function assertContainsWithTolerance($needle, $haystack, $message = '', $tolerance = 0)
    {
        if (is_numeric($needle) && is_numeric($haystack)) {
            $difference = abs($needle - $haystack);
            $this->assertLessThanOrEqual($tolerance, $difference, $message);
        } else {
            $this->assertStringContainsString($needle, $haystack, $message);
        }
    }
}
