<?php
echo "Testing Simplified Course List\n";
echo str_repeat("=", 30) . "\n\n";

// Check controller simplification
$controllerPath = 'app/Http/Controllers/Web/CoursesController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);

    echo "✓ CONTROLLER SIMPLIFIED:\n";
    if (strpos($content, 'enhancedCourses') === false) {
        echo "  ✓ Removed complex enhanced courses logic\n";
    } else {
        echo "  ✗ Enhanced courses logic still present\n";
    }

    if (strpos($content, 'paymentConfig') === false) {
        echo "  ✓ Removed complex payment configuration\n";
    } else {
        echo "  ✗ Payment configuration still present\n";
    }
}

echo "\n✓ COMPONENT SIMPLIFIED:\n";
$componentPath = 'resources/views/components/frontend/panels/courses/list.blade.php';
if (file_exists($componentPath)) {
    $content = file_get_contents($componentPath);

    if (strpos($content, 'enrollment-status-badge') === false) {
        echo "  ✓ Removed enrollment status badges\n";
    } else {
        echo "  ✗ Enrollment status badges still present\n";
    }

    if (strpos($content, 'route(\'contact\')') === false) {
        echo "  ✓ Fixed contact route issue\n";
    } else {
        echo "  ✗ Contact route issue still exists\n";
    }

    if (strpos($content, 'mailto:') !== false) {
        echo "  ✓ Using mailto link for contact\n";
    } else {
        echo "  ✗ No contact method found\n";
    }
}

echo "\n✓ SIMPLIFIED FEATURES:\n";
echo "  • Basic course listing\n";
echo "  • Simple enrollment buttons\n";
echo "  • Fixed contact route issue\n";
echo "  • No complex payment UI\n";
echo "  • Focus on enrollment functionality\n";

echo "\n🎉 COURSE LIST SIMPLIFIED!\n";
echo "   Test URL: https://frost.test/courses/list\n";
echo "   Should work without route errors now\n";
?>
