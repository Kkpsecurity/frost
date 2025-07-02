<?php namespace App\Support\Enum;

class OrderStatus
{
    const NEW           = 'New';
    const PROCESSING    = 'Processing';
    const SHIPPED       = 'Shipped';
    const COMPLETED     = 'Completed';
    const CANCELLED     = 'Cancelled';
    const ACTIVE        = 'Active';
    const EXPIRED       = 'Expired';

    public static function lists()
    {
        return [
            'all'                   => 'Show All Orders',
            self::NEW               => trans('app.order_status.'.self::NEW),
            self::PROCESSING        => trans('app.order_status.'.self::PROCESSING),
            self::SHIPPED           => trans('app.order_status.'.self::SHIPPED),
            self::COMPLETED         => trans('app.order_status.'.self::COMPLETED),
            self::CANCELLED         => trans('app.order_status.'.self::CANCELLED),
            self::ACTIVE            => trans('app.order_status.'.self::ACTIVE),
            self::EXPIRED           => trans('app.order_status.'.self::EXPIRED),
        ];
    }
}
