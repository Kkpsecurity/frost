<?php

namespace App\Presenters;

// timezone_identifiers_list()
// https://momentjs.com/docs/#/parsing/
//   'ddd MM/DD HH:mm'  Thu 03/24 15:21
//   'DD MMMM YYYY'     03 March 2022
//   'YYYY-MM-DD HH:mm:ss'

use Exception;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\User;


trait PresentsTimeStamps
{

    /**
     * Returns formatted created_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function CreatedAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('created_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted updated_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function UpdatedAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('updated_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted completed_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function CompletedAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('completed_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted expires_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function ExpiresAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('expires_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted dnc_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function DncAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('dnc_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted deact_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function DeactAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('deact_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted hidden_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function HiddenAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('hidden_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted refunded_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function RefundedAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('refunded_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted starts_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function StartsAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('starts_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted ends_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function EndsAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('ends_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted submitted_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function SubmittedAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('submitted_at', $carbon_format, $tz_user);
    }


    /**
     * Returns formatted next_attempt_at in User's timezone
     *
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function NextAttemptAt(?string $carbon_format = null, ?User $tz_user = null): ?string
    {
        return $this->FormatTimestamp('next_attempt_at', $carbon_format, $tz_user);
    }


    //
    //
    //


    /**
     * Returns formatted timestamp
     *
     * @param   string       $field
     * @param   string|null  $carbon_format
     * @param   User|null    $tz_user
     * @return  string|null
     */
    public function FormatTimestamp(string $field, ?string $carbon_format = null, ?User $tz_user = null): ?string
    {

        //
        // verify valid timestamp
        //   using model's $casts
        //

        if (! isset($this->casts[$field])) {
            throw new Exception(get_class($this) . " has no cast for '{$field}'");
        }


        $castType = $this->casts[$field];

        // Laravel commonly uses 'date' and 'datetime' (optionally with a format suffix like 'datetime:Y-m-d').
        // Older parts of this codebase also refer to 'timestamp' / 'timestamptz'.
        $allowedCastTypes = [
            'date',
            'datetime',
            'immutable_date',
            'immutable_datetime',
            'timestamp',
            'timestamptz',
        ];

        $isAllowed = false;
        foreach ($allowedCastTypes as $allowed) {
            if ($castType === $allowed || str_starts_with((string) $castType, $allowed . ':')) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            throw new Exception(get_class($this) . " '{$field}' is not a date or timestamp : '{$castType}'");
        }


        if (! $timestamp = $this->{$field}) {
            return null;
        }


        //
        // get User's preferred timezone (if possible)
        //

        $timezone = $this->UserTimezone($tz_user);


        //
        // convert UTC to selected timezone, then format
        //

        return Carbon::parse($timestamp, 'UTC')
            ->tz($timezone)
            ->isoFormat($carbon_format ?? config('define.carbon_format.default'));
    }


    /**
     * Returns User's selected timezone || Auth Users's timezone || system default timezone
     *
     * @param   User|null  $tz_user
     * @return  string
     */
    public function UserTimezone(?User $tz_user = null): string
    {

        $timezone = config('define.timezone.default');

        if ($tz_user) {
            return $tz_user->GetPref('timezone', $timezone);
        }

        if (Auth::check()) {
            return Auth::user()->GetPref('timezone', $timezone);
        }

        return $timezone;
    }
}
