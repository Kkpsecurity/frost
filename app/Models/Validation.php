<?php

namespace App\Models;

/**
 * @file Validation.php
 * @brief Model for validations table.
 * @details This model represents a validation record for course authorizations and student units,
 * including attributes like UUID, course authorization ID, student unit ID, status, ID type,
 * and reject reason. It also provides methods for checking validation status and managing file paths.
 */

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Helpers\TextTk;
use App\Traits\NoString;
use App\Models\CourseAuth;
use App\Models\StudentUnit;


class Validation extends Model
{

    const PATH_PRE = 'validations';
    const FILE_EXT = '.png';

    use NoString;


    protected $table        = 'validations';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;


    protected $casts        = [

        'id'                => 'integer',
        'uuid'              => 'string',
        'course_auth_id'    => 'integer',
        'student_unit_id'   => 'integer',
        'status'            => 'integer',  // [ -1, 0, 1 ]
        'id_type'           => 'string',   // 64
        'reject_reason'     => 'string',   // text

    ];

    protected $guarded      = ['id', 'uuid'];

    protected $attributes   = ['status' => 0];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    public function StudentUnit()
    {
        return $this->belongsTo(StudentUnit::class, 'student_unit_id');
    }


    //
    // incoming data filters
    //


    public function setIdTypeAttribute($value)
    {
        $this->attributes['id_type'] = TextTk::Sanitize($value);
    }

    public function setRejectReasonAttribute($value)
    {
        $this->attributes['reject_reason'] = TextTk::Sanitize($value);
    }


    //
    // helpers
    //


    public function IsChecked(): bool
    {
        return $this->status != 0;
    }


    public function IsValid(): bool
    {
        return $this->status > 0;
    }


    public function IsRejected(): bool
    {
        return $this->status < 0;
    }


    public function Accept(?string $id_type = null): void
    {

        if ($this->course_auth_id && ! $id_type) {
            throw new \Exception('ID Cards require an id_type');
        }

        $this->update([
            'status'        => 1,
            'id_type'       => $id_type,
            'reject_reason' => null,
        ]);
    }


    public function Reject(string $reject_reason): void
    {

        $this->update([
            'status'        => -1,
            'id_type'       => null,
            'reject_reason' => $reject_reason,
        ]);
    }


    //
    // pathing
    //


    public function RelPathBase(): string
    {
        $subfolder = $this->course_auth_id ? '/idcards/' : '/headshots/';

        // For ID cards, use course_auth_id_fullname pattern
        if ($this->course_auth_id) {
            $courseAuth = $this->CourseAuth;
            if ($courseAuth && $courseAuth->User) {
                $student = $courseAuth->User;
                $filenameBase = $this->course_auth_id . '_' .
                    strtolower(str_replace(' ', '_', $student->name));
                return 'media/' . self::PATH_PRE . $subfolder . $filenameBase;
            }
        }

        // For headshots, use student_unit_id pattern if available
        if (!$this->course_auth_id && $this->student_unit_id) {
            $studentUnit = $this->StudentUnit;
            if ($studentUnit && $studentUnit->Student) {
                $student = $studentUnit->Student;
                $filenameBase = $this->student_unit_id . '_' .
                    strtolower(str_replace(' ', '_', $student->name));
                return 'media/' . self::PATH_PRE . $subfolder . $filenameBase;
            }
        }

        // Fallback to UUID pattern
        return self::PATH_PRE . $subfolder . $this->uuid;
    }


    public function RelPathForExtension(string $extension): string
    {
        $ext = strtolower(trim($extension));
        if ($ext === '') {
            return $this->RelPathBase() . self::FILE_EXT;
        }

        if ($ext[0] !== '.') {
            $ext = '.' . $ext;
        }

        return $this->RelPathBase() . $ext;
    }


    public function RelPathResolved(): string
    {
        $base = $this->RelPathBase();

        $candidates = [
            $base . self::FILE_EXT,
            $base . '.jpg',
            $base . '.jpeg',
            $base . '.png',
            $base . '.webp',
        ];

        foreach ($candidates as $rel) {
            if (file_exists(storage_path('app/public/' . $rel))) {
                return $rel;
            }
        }

        return $base . self::FILE_EXT;
    }


    public function RelPath(): string
    {
        // Backward-compatible default path (may not exist if the file was stored with a different extension).
        return $this->RelPathBase() . self::FILE_EXT;
    }
    public function AbsPath(): string
    {
        return storage_path('app/public/' . $this->RelPathResolved());
    }


    public function URL(bool $return_blank_img = false): ?string
    {

        $resolved = $this->RelPathResolved();

        return file_exists(storage_path('app/public/' . $resolved))
            ? url('storage/' . $resolved)
            : ($return_blank_img ? asset('assets/img/no-image-500x300.png') : null);
    }
}
