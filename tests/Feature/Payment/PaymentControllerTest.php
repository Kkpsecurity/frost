<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use App\Events\Payment\PaymentCompleted;
use App\Events\Payment\PaymentFailed;
use App\Events\Enrollment\CourseEnrolled;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;

/**
 * PaymentControllerTest
 *
 * Feature tests for handleReturn() (PayFlowPro silent post-back) and
 * confirmStripe(). Each test runs against a fresh SQLite :memory: schema
 * built in setUp() — no migrations, no PG-specific SQL.
 *
 * Key assertions per payment path:
 *   - Payment record updated (status, transaction_id)
 *   - Order marked completed via SetCompleted()
 *   - CourseAuth row created
 *   - Correct event dispatched (PaymentCompleted | PaymentFailed)
 *   - CourseEnrolled fired as a side-effect of SetCompleted()
 */
class PaymentControllerTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Schema bootstrap
    // Each test gets a fresh SQLite :memory: connection — tables must be
    // created in every setUp() call.
    // -------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootTestSchema();
    }

    private function bootTestSchema(): void
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->boolean('is_active')->default(true);
            $table->string('name');
            $table->string('model_class');
            $table->string('controller_class');
        });

        DB::table('payment_types')->insert([
            'id'               => 1,
            'is_active'        => true,
            'name'             => 'PayFlowPro',
            'model_class'      => '\App\Models\Payments\PayFlowPro',
            'controller_class' => '\App\Http\Controllers\Payments\PayFlowProController',
        ]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('role_id')->default(5);
            $table->string('lname');
            $table->string('fname');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('use_gravatar')->default(false);
            $table->json('student_info')->nullable();
            $table->boolean('email_opt_in')->default(false);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('course_id');
            $table->unsignedSmallInteger('payment_type_id')->default(1);
            $table->decimal('course_price', 5, 2);
            $table->integer('discount_code_id')->nullable();
            $table->decimal('total_price', 5, 2);
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('course_auth_id')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedBigInteger('refunded_by')->nullable();
        });

        Schema::create('course_auths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('course_id');
            $table->timestamps();
            $table->timestamp('agreed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_passed')->default(false);
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->text('disabled_reason')->nullable();
            $table->boolean('id_override')->default(false);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payment_method')->default('payflowpro');
            $table->string('gateway')->default('payflowpro');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->text('gateway_response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    // -------------------------------------------------------------------------
    // Fixture helpers
    // -------------------------------------------------------------------------

    /**
     * total_price is always supplied so the OrderObserver skips its
     * RCache::Courses() lookup (which would require a courses table).
     */
    private function makeUser(string $email = 'student@test.com'): User
    {
        return User::create([
            'fname'    => 'Test',
            'lname'    => 'Student',
            'email'    => $email,
            'password' => bcrypt('password'),
        ]);
    }

    private function makeOrder(User $user, int $courseId = 1): Order
    {
        return Order::create([
            'user_id'         => $user->id,
            'course_id'       => $courseId,
            'payment_type_id' => 1,
            'course_price'    => '99.00',
            'total_price'     => '99.00',
        ]);
    }

    private function makePayment(Order $order): Payment
    {
        return Payment::create([
            'order_id'       => $order->id,
            'payment_method' => 'payflowpro',
            'gateway'        => 'payflowpro',
            'amount'         => $order->total_price,
            'currency'       => 'USD',
            'status'         => 'pending',
        ]);
    }

    // =========================================================================
    // handleReturn — PayFlowPro silent post-back (no auth middleware)
    // =========================================================================

    #[Test]
    public function payflowpro_approved_marks_payment_completed_with_pnref(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->post(route('payments.payflowpro.payment_return', $payment), [
            'RESULT'  => '0',
            'PNREF'   => 'A70A4F181C02',
            'PPREF'   => '9BU14101H0001234',
            'RESPMSG' => 'Approved',
        ])->assertRedirect(route('order.completed', $order));

        $this->assertDatabaseHas('payments', [
            'id'             => $payment->id,
            'status'         => 'completed',
            'transaction_id' => 'A70A4F181C02',
        ]);
    }

    #[Test]
    public function payflowpro_approved_calls_set_completed_and_creates_course_auth(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->post(route('payments.payflowpro.payment_return', $payment), [
            'RESULT'  => '0',
            'PNREF'   => 'A70A4F181C02',
            'PPREF'   => '9BU14101H0001234',
            'RESPMSG' => 'Approved',
        ]);

        $this->assertNotNull(
            $order->fresh()->completed_at,
            'Order.completed_at must be set after successful payment'
        );

        $this->assertDatabaseHas('course_auths', [
            'user_id'   => $user->id,
            'course_id' => $order->course_id,
        ]);
    }

    #[Test]
    public function payflowpro_approved_dispatches_payment_completed_and_course_enrolled_events(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->post(route('payments.payflowpro.payment_return', $payment), [
            'RESULT'  => '0',
            'PNREF'   => 'A70A4F181C02',
            'PPREF'   => '9BU14101H0001234',
            'RESPMSG' => 'Approved',
        ]);

        Event::assertDispatched(
            PaymentCompleted::class,
            fn($e) =>
            $e->order->id === $order->id
                && $e->payment->status === 'completed'
                && $e->payment->transaction_id === 'A70A4F181C02'
        );

        // SetCompleted() fires CourseEnrolled as a side-effect
        Event::assertDispatched(CourseEnrolled::class);
    }

    #[Test]
    public function payflowpro_declined_marks_payment_failed(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->post(route('payments.payflowpro.payment_return', $payment), [
            'RESULT'  => '12',
            'PNREF'   => '',
            'RESPMSG' => 'Card Declined',
        ]);

        $this->assertDatabaseHas('payments', [
            'id'     => $payment->id,
            'status' => 'failed',
        ]);
    }

    #[Test]
    public function payflowpro_declined_dispatches_payment_failed_event(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->post(route('payments.payflowpro.payment_return', $payment), [
            'RESULT'  => '12',
            'PNREF'   => '',
            'RESPMSG' => 'Card Declined',
        ]);

        Event::assertDispatched(
            PaymentFailed::class,
            fn($e) =>
            $e->order->id === $order->id
                && $e->reason === 'Card Declined'
        );
    }

    #[Test]
    public function payflowpro_declined_does_not_create_course_auth_or_complete_order(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->post(route('payments.payflowpro.payment_return', $payment), [
            'RESULT'  => '12',
            'PNREF'   => '',
            'RESPMSG' => 'Card Declined',
        ]);

        $this->assertDatabaseMissing('course_auths', ['user_id' => $user->id]);
        $this->assertNull(
            $order->fresh()->completed_at,
            'Order must not be completed on a declined payment'
        );
    }

    #[Test]
    public function payflowpro_missing_result_field_is_treated_as_failure(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        // POST with no RESULT key — handleReturn() defaults to -1
        $this->post(route('payments.payflowpro.payment_return', $payment), []);

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'failed']);
        Event::assertDispatched(PaymentFailed::class);
        $this->assertDatabaseMissing('course_auths', ['user_id' => $user->id]);
    }

    // =========================================================================
    // confirmStripe
    // =========================================================================

    #[Test]
    public function confirm_stripe_rejects_user_who_does_not_own_the_payment(): void
    {
        Event::fake();
        $owner   = $this->makeUser('owner@test.com');
        $other   = $this->makeUser('other@test.com');
        $order   = $this->makeOrder($owner);
        $payment = $this->makePayment($order);

        $this->actingAs($other)
            ->postJson(route('payments.stripe.confirm', $payment), [
                'payment_intent_id' => 'pi_test_123',
            ])
            ->assertStatus(403);
    }

    #[Test]
    public function confirm_stripe_marks_payment_completed_with_payment_intent_id(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->actingAs($user)
            ->postJson(route('payments.stripe.confirm', $payment), [
                'payment_intent_id' => 'pi_test_abc123',
                'payment_method'    => 'pm_card_visa',
            ])
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('payments', [
            'id'             => $payment->id,
            'status'         => 'completed',
            'transaction_id' => 'pi_test_abc123',
        ]);
    }

    #[Test]
    public function confirm_stripe_calls_set_completed_and_creates_course_auth(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->actingAs($user)
            ->postJson(route('payments.stripe.confirm', $payment), [
                'payment_intent_id' => 'pi_test_abc123',
            ]);

        $this->assertNotNull(
            $order->fresh()->completed_at,
            'Order.completed_at must be set after Stripe confirmation'
        );

        $this->assertDatabaseHas('course_auths', [
            'user_id'   => $user->id,
            'course_id' => $order->course_id,
        ]);
    }

    #[Test]
    public function confirm_stripe_dispatches_payment_completed_and_course_enrolled_events(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->actingAs($user)
            ->postJson(route('payments.stripe.confirm', $payment), [
                'payment_intent_id' => 'pi_test_abc123',
            ]);

        Event::assertDispatched(
            PaymentCompleted::class,
            fn($e) =>
            $e->order->id === $order->id
                && $e->payment->transaction_id === 'pi_test_abc123'
        );

        Event::assertDispatched(CourseEnrolled::class);
    }

    #[Test]
    public function confirm_stripe_requires_payment_intent_id(): void
    {
        Event::fake();
        $user    = $this->makeUser();
        $order   = $this->makeOrder($user);
        $payment = $this->makePayment($order);

        $this->actingAs($user)
            ->postJson(route('payments.stripe.confirm', $payment), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payment_intent_id']);
    }
}
