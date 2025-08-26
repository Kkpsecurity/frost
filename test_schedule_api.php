<?php
require_once 'vendor/autoload.php';

use App\Http\Controllers\Web\Courses\CourseController;
use Illuminate\Http\Request;

// Create Laravel app instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a simple request
$request = Illuminate\Http\Request::create('/api/courses/schedule-data', 'GET');

try {
    // Test the controller directly
    $controller = new CourseController();
    $response = $controller->getScheduleData($request);

    echo "Controller response:\n";
    echo $response->getContent();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
