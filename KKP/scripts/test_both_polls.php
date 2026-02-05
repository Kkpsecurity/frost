<!DOCTYPE html>
<html>
<head>
    <title>Test Both Polls</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        .poll-section { margin: 20px 0; padding: 20px; background: #000; border: 2px solid #0f0; }
        h2 { color: #0ff; }
        pre { background: #222; padding: 15px; overflow-x: auto; border-left: 3px solid #0f0; }
        .error { color: #f00; border-color: #f00; }
        .success { border-color: #0f0; }
    </style>
</head>
<body>
    <h1>🔍 DUAL POLL DIAGNOSTIC</h1>

    <?php
    // Cookie value from browser
    $laravelSession = 'eyJpdiI6IjJROTFuNmp6d1pDWXlTSGlUblF0NXc9PSIsInZhbHVlIjoiZmlMcGFNUkRyUjF1cE02MGd1eVdLNVFJY1RmdEdjcjY2a0gvdFFXQU5rYTQ4S0VQREdkTFE5WjlqQjVheTVzQ0MzYnBrblYwMjV0OWwxQytNOHhtVndQKzliSXN1NmZQWFNZRzNJZGF1RGQvdXFQM2FqL01SMG95NXVuQUpxVFkiLCJtYWMiOiJmMjdmOTk2YWY1MzU2MjI3NTE5NDgwZTM3NWEwNmRkOGQxNzViNTFlNTY1MWU3ZGUwYWU5ZThmZjY3OGU4ZjMxIiwidGFnIjoiIn0%3D';
    $xsrfToken = 'eyJpdiI6IjNEb2h4Nm5EeWkxSDlGeGxTMjU0bmc9PSIsInZhbHVlIjoiY0RvS0ZROERCZkZCZkdOZjZ0OXUwZEc3aUU3V2Mxd1gzZkdwSXhWVVl4WDlIY2ZDVFZTS3M1anpmREhEYTVOOFNNMjZBU3FaSGpSS1I5YkQvWmcyZ0FGNEFQL2Z4Zjh1VjRLUEpmZDgySHRtdjR5M3hKcDY1dU5RaTJlT0d4MGUiLCJtYWMiOiIzN2Y3ZGJlZjRjOGFkOTM1ODQ2ZjM3MjNmNGI3MDdmMGZjODIxOGU5ZDg0OGE0ZDk0OWFiNGZjOGY2MDg4NDE1IiwidGFnIjoiIn0%3D';

    $baseUrl = 'http://frost.test';

    function makeRequest($url, $sessionCookie, $xsrfToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Cookie: laravel_session=' . $sessionCookie . '; XSRF-TOKEN=' . $xsrfToken,
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'response' => $response,
            'data' => json_decode($response, true)
        ];
    }

    // Test Student Poll
    echo '<div class="poll-section">';
    echo '<h2>1️⃣ STUDENT POLL: /classroom/student/poll</h2>';
    echo '<p>Purpose: Student-owned data (progress, identity, enrollment)</p>';

    $studentPoll = makeRequest($baseUrl . '/classroom/student/poll', $laravelSession, $xsrfToken);

    if ($studentPoll['code'] === 200) {
        echo '<div class="success">';
        echo '<h3>✅ HTTP ' . $studentPoll['code'] . '</h3>';

        $data = $studentPoll['data'];
        if (isset($data['data'])) {
            echo '<h4>Student Data Keys:</h4>';
            echo '<pre>' . implode("\n", array_keys($data['data'])) . '</pre>';

            if (isset($data['data']['lessons'])) {
                echo '<h4>Student Lessons:</h4>';
                echo '<pre>';
                foreach ($data['data']['lessons'] as $lesson) {
                    echo "Lesson {$lesson['id']}: {$lesson['title']}\n";
                    echo "  - completed_at: " . ($lesson['completed_at'] ?? 'NULL') . "\n";
                    echo "  - score: " . ($lesson['score'] ?? 'N/A') . "\n\n";
                }
                echo '</pre>';
            }
        }

        echo '<h4>Full Response:</h4>';
        echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<h3>❌ HTTP ' . $studentPoll['code'] . '</h3>';
        echo '<pre>' . htmlspecialchars($studentPoll['response']) . '</pre>';
        echo '</div>';
    }
    echo '</div>';

    // Test Classroom Poll
    echo '<div class="poll-section">';
    echo '<h2>2️⃣ CLASSROOM POLL: /classroom/class/data</h2>';
    echo '<p>Purpose: Classroom-owned data (InstLessons, Zoom, instructor status)</p>';

    $classroomPoll = makeRequest($baseUrl . '/classroom/class/data', $laravelSession, $xsrfToken);

    if ($classroomPoll['code'] === 200) {
        echo '<div class="success">';
        echo '<h3>✅ HTTP ' . $classroomPoll['code'] . '</h3>';

        $data = $classroomPoll['data'];
        if (isset($data['data'])) {
            echo '<h4>Classroom Data Keys:</h4>';
            echo '<pre>' . implode("\n", array_keys($data['data'])) . '</pre>';

            echo '<h4>🎯 ACTIVE LESSON ID:</h4>';
            echo '<pre>active_lesson_id: ' . json_encode($data['data']['active_lesson_id'] ?? 'NOT PRESENT') . '</pre>';

            if (isset($data['data']['lessons'])) {
                echo '<h4>Classroom Lessons:</h4>';
                echo '<pre>';
                foreach ($data['data']['lessons'] as $lesson) {
                    echo "Lesson {$lesson['id']}: {$lesson['title']}\n";
                    echo "  - status: {$lesson['status']}\n";
                    echo "  - is_active: " . json_encode($lesson['is_active']) . "\n";
                    echo "  - is_completed: " . json_encode($lesson['is_completed']) . "\n";
                    echo "  - started_at: " . ($lesson['started_at'] ?? 'NULL') . "\n\n";
                }
                echo '</pre>';
            }

            if (isset($data['data']['inst_lessons'])) {
                echo '<h4>📚 InstLessons (from Instructor):</h4>';
                echo '<pre>';
                foreach ($data['data']['inst_lessons'] as $instLesson) {
                    echo "InstLesson {$instLesson['id']}:\n";
                    echo "  - lesson_id: {$instLesson['lesson_id']}\n";
                    echo "  - lesson_title: " . ($instLesson['lesson']['title'] ?? 'N/A') . "\n";
                    echo "  - completed_at: " . ($instLesson['completed_at'] ?? 'NULL') . "\n\n";
                }
                echo '</pre>';
            }
        }

        echo '<h4>Full Response:</h4>';
        echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<h3>❌ HTTP ' . $classroomPoll['code'] . '</h3>';
        echo '<pre>' . htmlspecialchars($classroomPoll['response']) . '</pre>';
        echo '</div>';
    }
    echo '</div>';

    // Comparison
    echo '<div class="poll-section">';
    echo '<h2>🔍 COMPARISON & ANALYSIS</h2>';

    if ($studentPoll['code'] === 200 && $classroomPoll['code'] === 200) {
        $studentData = $studentPoll['data']['data'] ?? [];
        $classData = $classroomPoll['data']['data'] ?? [];

        echo '<h3>Active Lesson Detection:</h3>';
        echo '<pre>';
        echo "Classroom active_lesson_id: " . json_encode($classData['active_lesson_id'] ?? null) . "\n";
        echo "Student has InstLesson data: " . (isset($studentData['inst_lesson']) ? 'YES' : 'NO') . "\n";
        echo '</pre>';

        echo '<h3>Lessons Comparison:</h3>';
        echo '<pre>';
        $studentLessons = $studentData['lessons'] ?? [];
        $classroomLessons = $classData['lessons'] ?? [];

        echo "Student Poll returned: " . count($studentLessons) . " lessons\n";
        echo "Classroom Poll returned: " . count($classroomLessons) . " lessons\n\n";

        if (!empty($classroomLessons)) {
            echo "Classroom Lesson Statuses:\n";
            foreach ($classroomLessons as $lesson) {
                echo "  - Lesson {$lesson['id']}: status={$lesson['status']}, is_active=" . json_encode($lesson['is_active']) . "\n";
            }
        }
        echo '</pre>';
    }
    echo '</div>';
    ?>
</body>
</html>
