<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Carbon\Carbon;
use Laravel\Scout\Searchable;
use Lab404\Impersonate\Models\Impersonate;

use App\Services\RCache;

use App\Models\Role;
use App\Models\UserPref;
use App\Models\InstLicense;
use App\Models\UserBrowser;
use App\Models\Traits\User\ExamsTrait;
use App\Models\Traits\User\RolesTrait;
use App\Models\Traits\User\UserPrefsTrait;
use App\Models\Traits\User\CourseAuthsTrait;
use App\Models\Traits\User\UserBrowserTrait;

use App\Casts\JSONCast;
use App\Helpers\TextTk;
// use App\Traits\Observable; // Temporarily disabled - missing observer
use App\Traits\AvatarTrait;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


class User extends Authenticatable implements MustVerifyEmail
{

    #use HasApiTokens, HasFactory;

    use Notifiable; // Observable temporarily disabled
    use PgTimestamps, PresentsTimeStamps;
    use CourseAuthsTrait, ExamsTrait, RolesTrait, UserBrowserTrait, UserPrefsTrait;
    use AvatarTrait, Searchable, Impersonate;

    const SEARCHABLE_FIELDS = ['id', 'email', 'fname', 'lname'];

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',

        'is_active' => 'boolean',
        'role_id' => 'integer',

        'lname' => 'string',
        // 255
        'fname' => 'string',
        // 255
        'email' => 'string',
        // 255

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',

        'password' => 'string',
        // 100
        'remember_token' => 'string',
        // 100

        'avatar' => 'string',
        'use_gravatar' => 'boolean',

        'student_info' => JSONCast::class,
        #'student_info'      => 'array',

        'email_opt_in' => 'boolean',
    ];

    protected $fillable = [
        'lname',
        'fname',
        'email',
        'password',
        'remember_token',
        'avatar',
        'use_gravatar',
        'student_info',
    ];

    // public $dates = [
    //     'created_at',
    //     'updated_at',
    //     'email_verified_at'
    // ];



    protected $attributes = [
        'is_active' => true,
        'role_id' => 5, // default: student
        'email_opt_in' => false,
    ];

    protected $guarded = [
        'id',
        'is_active',
        'role_id',
        'zoom_creds_id',
    ];

    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
    ];

    public function fullname()
    {
        return "{$this->fname} {$this->lname}";
    }

    public function getNameAttribute()
    {
        return $this->fullname();
    }

    public function __toString()
    {
        return $this->fullname();
    }

    //
    // external package requirements
    //

    public function canImpersonate()
    {
        return $this->role_id == 1;
    }


    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'fname' => $this->fname,
            'lname' => $this->lname,
        ];
    }

    //
    // relationships
    //

    public function CourseAuths()
    {
        return $this->hasMany(CourseAuth::class, 'user_id');
    }

    public function InstLicenses()
    {
        return $this->hasMany(InstLicense::class, 'user_id');
    }

    public function Role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function UserBrowser()
    {
        return $this->hasOne(UserBrowser::class, 'user_id');
    }

    public function UserPrefs()
    {
        return $this->hasMany(UserPref::class, 'user_id');
    }

    //
    // incoming data filters
    //

    public function setLnameAttribute($value)
    {
        $this->attributes['lname'] = TextTk::Sanitize($value);
    }

    public function setFnameAttribute($value)
    {
        $this->attributes['fname'] = TextTk::Sanitize($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = TextTk::Sanitize($value);
    }

    //
    // cache queries
    //

    public function GetRole(): Role
    {
        return RCache::Roles($this->role_id);
    }

    //
    // email
    //

    public function hasDLicense(): bool
    {
        return false;
    }

    public function hasGLicense(): bool
    {
        return false;
    }


    public function emailTo(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->fullname()
        ];
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->is_active; // Only index active users
    }
}
