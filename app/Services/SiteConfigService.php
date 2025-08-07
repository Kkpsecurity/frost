<?php

namespace App\Services;

use Akaunting\Setting\Facade as Setting;

class SiteConfigService
{
    /**
     * Get all site-related settings
     */
    public function getSiteSettings(): array
    {
        return [
            'company_name' => Setting::get('site.company_name', 'Florida Online Security Training'),
            'company_address' => Setting::get('site.company_address', ''),
            'contact_email' => Setting::get('site.contact_email', ''),
            'support_email' => Setting::get('site.support_email', ''),
            'support_phone' => Setting::get('site.support_phone', ''),
            'support_phone_hours' => Setting::get('site.support_phone_hours', ''),
            'google_map_url' => Setting::get('site.google_map_url', ''),
        ];
    }

    /**
     * Get all class timing settings
     */
    public function getClassSettings(): array
    {
        return [
            'starts_soon_seconds' => (int) Setting::get('class.starts_soon_seconds', 14400),
            'is_ended_seconds' => (int) Setting::get('class.is_ended_seconds', 600),
            'zoom_duration_minutes' => (int) Setting::get('class.zoom_duration_minutes', 15),
        ];
    }

    /**
     * Get all student-related settings
     */
    public function getStudentSettings(): array
    {
        return [
            'lesson_complete_seconds' => (int) Setting::get('student.lesson_complete_seconds', 120),
            'poll_seconds' => (int) Setting::get('student.poll_seconds', 30),
            'join_lesson_seconds' => (int) Setting::get('student.join_lesson_seconds', 300),
        ];
    }

    /**
     * Get all instructor-related settings
     */
    public function getInstructorSettings(): array
    {
        return [
            'close_lesson_minutes' => (int) Setting::get('instructor.close_lesson_minutes', 30),
            'pre_start_minutes' => (int) Setting::get('instructor.pre_start_minutes', 120),
            'post_end_minutes' => (int) Setting::get('instructor.post_end_minutes', 30),
            'next_lesson_seconds' => (int) Setting::get('instructor.next_lesson_seconds', 300),
            'close_unit_seconds' => (int) Setting::get('instructor.close_unit_seconds', 300),
        ];
    }

    /**
     * Get chat settings
     */
    public function getChatSettings(): array
    {
        return [
            'log_last' => (int) Setting::get('chat.log_last', 50),
        ];
    }

    /**
     * Get all settings grouped by category
     */
    public function getAllSettings(): array
    {
        return [
            'site' => $this->getSiteSettings(),
            'class' => $this->getClassSettings(),
            'student' => $this->getStudentSettings(),
            'instructor' => $this->getInstructorSettings(),
            'chat' => $this->getChatSettings(),
        ];
    }

    /**
     * Update a site setting
     */
    public function setSiteSetting(string $key, $value): void
    {
        Setting::set("site.{$key}", $value);
    }

    /**
     * Update a class setting
     */
    public function setClassSetting(string $key, $value): void
    {
        Setting::set("class.{$key}", $value);
    }

    /**
     * Update a student setting
     */
    public function setStudentSetting(string $key, $value): void
    {
        Setting::set("student.{$key}", $value);
    }

    /**
     * Update an instructor setting
     */
    public function setInstructorSetting(string $key, $value): void
    {
        Setting::set("instructor.{$key}", $value);
    }

    /**
     * Update a chat setting
     */
    public function setChatSetting(string $key, $value): void
    {
        Setting::set("chat.{$key}", $value);
    }
}
