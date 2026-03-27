<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\SupportController;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class SupportControllerToCarbonTest extends TestCase
{
    private function callToCarbon(mixed $value): ?Carbon
    {
        $controller = new SupportController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('toCarbon');
        $method->setAccessible(true);

        /** @var Carbon|null $result */
        $result = $method->invoke($controller, $value);

        return $result;
    }

    public function test_to_carbon_returns_null_for_empty_values(): void
    {
        $this->assertNull($this->callToCarbon(null));
        $this->assertNull($this->callToCarbon(''));
        $this->assertNull($this->callToCarbon('   '));
        $this->assertNull($this->callToCarbon(0));
        $this->assertNull($this->callToCarbon('0'));
    }

    public function test_to_carbon_accepts_datetime_interface(): void
    {
        $dt = new \DateTimeImmutable('2026-03-26 12:34:56', new \DateTimeZone('UTC'));
        $carbon = $this->callToCarbon($dt);

        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame($dt->getTimestamp(), $carbon->timestamp);
    }

    public function test_to_carbon_accepts_carbon_interface(): void
    {
        $original = Carbon::parse('2026-03-26 12:34:56', 'UTC');
        $carbon = $this->callToCarbon($original);

        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame($original->timestamp, $carbon->timestamp);
    }

    public function test_to_carbon_accepts_numeric_seconds_timestamp(): void
    {
        $timestamp = 1_700_000_000;
        $carbon = $this->callToCarbon($timestamp);

        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame($timestamp, $carbon->timestamp);

        $carbonFromString = $this->callToCarbon((string) $timestamp);
        $this->assertInstanceOf(Carbon::class, $carbonFromString);
        $this->assertSame($timestamp, $carbonFromString->timestamp);
    }

    public function test_to_carbon_accepts_numeric_millisecond_timestamp(): void
    {
        $milliseconds = 1_700_000_000_000;
        $expectedSeconds = 1_700_000_000;

        $carbon = $this->callToCarbon($milliseconds);

        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame($expectedSeconds, $carbon->timestamp);
    }

    public function test_to_carbon_accepts_parseable_string(): void
    {
        $carbon = $this->callToCarbon('2026-03-26 12:34:56');
        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame('2026-03-26 12:34:56', $carbon->format('Y-m-d H:i:s'));
    }
}
