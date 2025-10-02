<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

use App\Services\RCache;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\PaymentType;
use App\Models\DiscountCode;
use App\Models\Payment;
use App\Models\Payments\PaymentModel;

use App\Models\Traits\Order\CalcPrice;
use App\Models\Traits\Order\SetCompleted;
use App\Models\Traits\Order\DiscountCodeTrait;

use App\Traits\NoString;
use App\Traits\Observable;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


class Order extends Model
{

    #use Notifiable;
    #use OrderPresenter;
    use CalcPrice, DiscountCodeTrait, SetCompleted;
    use PgTimestamps, PresentsTimeStamps;
    use NoString, Observable;


    protected $table        = 'orders';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'user_id'           => 'integer',
        'course_id'         => 'integer',

        'payment_type_id'   => 'integer',

        'course_price'      => 'decimal:2',
        'discount_code_id'  => 'integer',
        'total_price'       => 'decimal:2',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime',

        'course_auth_id'    => 'integer',

        'refunded_at' => 'datetime',
        'refunded_by'       => 'integer',

    ];

    protected $guarded      = ['id'];


    //
    // relationships
    //


    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function CourseAuth()
    {
        return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    public function DiscountCode()
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }

    public function PaymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function RefundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    //
    // cache queries
    //


    public function GetCourse(): Course
    {
        return RCache::Courses($this->course_id);
    }

    public function GetDiscountCode(): ?DiscountCode
    {
        return ($this->discount_code_id ? RCache::DiscountCodes($this->discount_code_id) : null);
    }

    public function GetPaymentType(): PaymentType
    {
        return RCache::PaymentTypes($this->payment_type_id);
    }

    public function GetRefundedBy(): ?User
    {
        return RCache::Admin($this->user_id);
    }

    public function GetUser(): User
    {
        return RCache::User($this->user_id);
    }


    //
    // helpers
    //

    /**
     * Get order number attribute (formatted ID)
     */
    public function getOrderNumberAttribute(): string
    {
        return 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get order status based on timestamps
     */
    public function getStatusAttribute(): string
    {
        if ($this->refunded_at) {
            return 'Cancelled';
        }

        if ($this->completed_at) {
            return 'Completed';
        }

        return 'Processing';
    }

    /**
     * Get total attribute (alias for total_price)
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->total_price;
    }

    /**
     * Get subtotal attribute (alias for course_price)
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->course_price;
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if ($this->discount_code_id && $this->DiscountCode) {
            $discountCode = $this->DiscountCode;
            if ($discountCode->percent && $discountCode->percent > 0) {
                return $this->course_price * ($discountCode->percent / 100);
            } elseif ($discountCode->set_price && $discountCode->set_price > 0) {
                return (float) $discountCode->set_price;
            }
        }
        return 0.0;
    }

    /**
     * Get tax amount
     */
    public function getTaxAmountAttribute(): float
    {
        // For now, return 0 as tax calculation can be added later
        return 0.0;
    }

    /**
     * Get notes attribute (return empty string since field doesn't exist in database)
     */
    public function getNotesAttribute(): string
    {
        return '';
    }


    public function GetPayment(): ?PaymentModel
    {
        return (
            RCache::PaymentTypes($this->payment_type_id)->model_class
        )::firstWhere('order_id', $this->id);
    }

    /**
     * Get all payments for this order (new Payment model)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function AllPayments(): Collection
    {

        $Payments = collect([]);

        foreach (RCache::PaymentTypes() as $PaymentType) {
            if ($Payment = ($PaymentType->model_class)::firstWhere('order_id', $this->id)) {
                $Payments->push($Payment);
            }
        }

        return $Payments;
    }


    public function CanRefund(): bool
    {

        if (
            ! $this->completed_at
            or $this->refunded_at
            or $this->total_price == '0.00'
        ) {
            return false;
        }

        return $this->GetPayment()->CanRefund();
    }
}
