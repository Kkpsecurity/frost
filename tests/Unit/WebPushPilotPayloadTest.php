<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\ExamAuth;
use App\Notifications\Exam\ExamAuthorizedNotification;
use App\Notifications\Preparation\ClassTomorrowNotification;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Unit tests for the toWebPush() payload of the two pilot Web Push notifications.
 *
 * These tests verify that the notifications deliver a correctly-structured
 * payload array when the WebPushChannel calls ->toWebPush().  They use
 * real (unsaved) Eloquent model instances with setRelation() / forceFill()
 * to avoid any DB dependency while satisfying the constructor type hints.
 *
 * Extends Tests\TestCase (rather than PHPUnit\Framework\TestCase) so that
 * the Laravel app is booted and route() helpers work inside toWebPush().
 * No RefreshDatabase trait — these tests make zero database writes.
 */
class WebPushPilotPayloadTest extends TestCase
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Builds a stub notifiable (User). */
    private function notifiable(): object
    {
        return new class {
            public int    $id    = 1;
            public string $fname = 'Jane';
            public string $email = 'jane@example.com';
        };
    }

    /**
     * Builds a ClassTomorrowNotification with real (unsaved) Eloquent instances.
     */
    private function makeTomorrowNotification(
        string $courseName   = 'DPS-7',
        string $classTime    = '9:00 AM',
        int    $courseDateId = 42
    ): ClassTomorrowNotification {
        // Build Course → CourseAuth relationship chain
        $course = new Course();
        $course->forceFill(['title' => $courseName]);

        $courseAuth = new CourseAuth();
        $courseAuth->setRelation('Course', $course);

        // starts_at cast is 'datetime' — pass a Carbon so the cast is a no-op
        $startsAt = Carbon::now()->setTimeFromTimeString('09:00:00');

        $courseDate = new CourseDate();
        $courseDate->forceFill(['id' => $courseDateId, 'starts_at' => $startsAt]);

        return new ClassTomorrowNotification($courseAuth, $courseDate);
    }

    /**
     * Builds an ExamAuthorizedNotification with real (unsaved) Eloquent instances.
     */
    private function makeExamNotification(
        string $courseName   = 'DPS-7',
        int    $courseAuthId = 100,
        int    $examAuthId   = 55
    ): ExamAuthorizedNotification {
        $course = new Course();
        $course->forceFill(['title' => $courseName]);

        $courseAuth = new CourseAuth();
        $courseAuth->forceFill(['id' => $courseAuthId]);
        $courseAuth->setRelation('Course', $course);

        $examAuth = new ExamAuth();
        $examAuth->forceFill(['id' => $examAuthId, 'course_auth_id' => $courseAuthId]);
        $examAuth->setRelation('CourseAuth', $courseAuth);

        return new ExamAuthorizedNotification($examAuth);
    }

    // ─── ClassTomorrowNotification ────────────────────────────────────────────

    public function test_tomorrow_payload_contains_title(): void
    {
        $n       = $this->makeTomorrowNotification('DPS-7');
        $payload = $n->toWebPush($this->notifiable());

        $this->assertArrayHasKey('title', $payload);
        $this->assertStringContainsString('DPS-7', $payload['title']);
    }

    public function test_tomorrow_payload_contains_body(): void
    {
        $n       = $this->makeTomorrowNotification('DPS-7', '10:00 AM');
        $payload = $n->toWebPush($this->notifiable());

        $this->assertArrayHasKey('body', $payload);
        $this->assertNotEmpty($payload['body']);
        $this->assertIsString($payload['body']);
    }

    public function test_tomorrow_payload_contains_string_url(): void
    {
        $n       = $this->makeTomorrowNotification();
        $payload = $n->toWebPush($this->notifiable());

        $this->assertArrayHasKey('url', $payload);
        $this->assertIsString($payload['url']);
        $this->assertNotEmpty($payload['url']);
    }

    public function test_tomorrow_payload_tag_is_unique_per_course_date(): void
    {
        $n1 = $this->makeTomorrowNotification(courseDateId: 1);
        $n2 = $this->makeTomorrowNotification(courseDateId: 2);

        $tag1 = $n1->toWebPush($this->notifiable())['tag'] ?? null;
        $tag2 = $n2->toWebPush($this->notifiable())['tag'] ?? null;

        $this->assertNotNull($tag1);
        $this->assertNotNull($tag2);
        $this->assertNotSame($tag1, $tag2, 'Tags must differ for different course dates');
    }

    public function test_tomorrow_payload_icon_path_is_string(): void
    {
        $n       = $this->makeTomorrowNotification();
        $payload = $n->toWebPush($this->notifiable());

        if (array_key_exists('icon', $payload)) {
            $this->assertIsString($payload['icon']);
        }

        $this->assertTrue(true); // icon is optional
    }

    public function test_tomorrow_via_includes_webpush_channel(): void
    {
        // Stub notifiable with no UserPrefs so defaults apply
        $notifiable = new class {
            public int $id = 1;
        };

        $n        = $this->makeTomorrowNotification();
        $channels = $n->via($notifiable);

        $this->assertContains('webpush', $channels);
    }

    // ─── ExamAuthorizedNotification ───────────────────────────────────────────

    public function test_exam_payload_contains_title(): void
    {
        $n       = $this->makeExamNotification('DPS-7');
        $payload = $n->toWebPush($this->notifiable());

        $this->assertArrayHasKey('title', $payload);
        $this->assertStringContainsString('DPS-7', $payload['title']);
    }

    public function test_exam_payload_contains_body(): void
    {
        $n       = $this->makeExamNotification();
        $payload = $n->toWebPush($this->notifiable());

        $this->assertArrayHasKey('body', $payload);
        $this->assertIsString($payload['body']);
        $this->assertNotEmpty($payload['body']);
    }

    public function test_exam_payload_url_contains_course_auth_id(): void
    {
        $n       = $this->makeExamNotification(courseAuthId: 100);
        $payload = $n->toWebPush($this->notifiable());

        $this->assertArrayHasKey('url', $payload);
        $this->assertStringContainsString('100', (string) $payload['url']);
    }

    public function test_exam_payload_tag_is_unique_per_exam_auth(): void
    {
        $n1 = $this->makeExamNotification(examAuthId: 10);
        $n2 = $this->makeExamNotification(examAuthId: 20);

        $tag1 = $n1->toWebPush($this->notifiable())['tag'] ?? null;
        $tag2 = $n2->toWebPush($this->notifiable())['tag'] ?? null;

        $this->assertNotSame($tag1, $tag2, 'Tags must differ for different exam auths');
    }

    public function test_exam_payload_title_contains_checkmark_or_course_name(): void
    {
        $n       = $this->makeExamNotification('Safety101');
        $payload = $n->toWebPush($this->notifiable());

        $hasCheckmark  = str_contains($payload['title'], '✅');
        $hasCourseName = str_contains($payload['title'], 'Safety101');

        $this->assertTrue(
            $hasCheckmark || $hasCourseName,
            "Title should contain a checkmark or the course name. Got: {$payload['title']}"
        );
    }

    // ─── Shared payload contract ──────────────────────────────────────────────

    /**
     * @dataProvider pilotNotificationProvider
     */
    public function test_payload_is_an_array(string $type): void
    {
        $n       = $type === 'tomorrow'
            ? $this->makeTomorrowNotification()
            : $this->makeExamNotification();
        $payload = $n->toWebPush($this->notifiable());

        $this->assertIsArray($payload);
    }

    /**
     * @dataProvider pilotNotificationProvider
     */
    public function test_payload_title_is_non_empty_string(string $type): void
    {
        $n       = $type === 'tomorrow'
            ? $this->makeTomorrowNotification()
            : $this->makeExamNotification();
        $payload = $n->toWebPush($this->notifiable());

        $this->assertNotEmpty($payload['title'] ?? '');
    }

    /**
     * @dataProvider pilotNotificationProvider
     */
    public function test_payload_body_is_non_empty_string(string $type): void
    {
        $n       = $type === 'tomorrow'
            ? $this->makeTomorrowNotification()
            : $this->makeExamNotification();
        $payload = $n->toWebPush($this->notifiable());

        $this->assertNotEmpty($payload['body'] ?? '');
    }

    /**
     * @dataProvider pilotNotificationProvider
     */
    public function test_payload_is_valid_json_encodable(string $type): void
    {
        $n       = $type === 'tomorrow'
            ? $this->makeTomorrowNotification()
            : $this->makeExamNotification();
        $payload = $n->toWebPush($this->notifiable());

        $json = json_encode($payload);

        $this->assertNotFalse($json);
        $this->assertJson($json);
    }

    public static function pilotNotificationProvider(): array
    {
        return [
            'ClassTomorrow'    => ['tomorrow'],
            'ExamAuthorized'   => ['exam'],
        ];
    }
}
