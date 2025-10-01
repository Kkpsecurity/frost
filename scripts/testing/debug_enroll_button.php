<?php

echo "Simple Enrollment URL Test\n";
echo "==========================\n\n";

// Test direct URL construction
$courseId = 1;
$baseUrl = "https://frost.test";
$enrollUrl = "$baseUrl/courses/enroll/$courseId";

echo "Testing enrollment URL access:\n";
echo "URL: $enrollUrl\n\n";

// Check if course detail page shows correct enrollment URL
echo "Check the following in your browser:\n";
echo "1. Go to: https://frost.test/courses/1\n";
echo "2. Right-click 'Enroll Now' button → 'Inspect Element'\n";
echo "3. Look at the href attribute\n";
echo "4. Open browser console (F12) and look for the console.log message when clicking\n";
echo "5. Check Network tab to see if any requests are made\n\n";

echo "Expected href should be: $enrollUrl\n";
echo "If href is empty or incorrect, there's a route generation issue\n";
echo "If href is correct but nothing happens, there might be JavaScript interference\n\n";

echo "Manual test steps:\n";
echo "1. Try typing the URL directly: $enrollUrl\n";
echo "2. If that works, the issue is with the button\n";
echo "3. If that doesn't work, the issue is with the route/controller\n";
