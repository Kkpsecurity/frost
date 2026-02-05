<?php

declare(strict_types=1);

namespace App\Classes;

use Illuminate\Support\Facades\Storage;

/**
 * Manages instructor chat preset messages stored in JSON files
 * File format: instructor_chat_message_{instructor_ID}.json
 * Location: storage/app/instructor_chat_presets/
 */
class InstructorChatPresets
{
    private const PRESETS_DIRECTORY = 'instructor_chat_presets';

    /**
     * Get the file path for an instructor's presets
     */
    private static function getFilePath(int $instructorId): string
    {
        return self::PRESETS_DIRECTORY . "/instructor_chat_message_{$instructorId}.json";
    }

    /**
     * Get all preset messages for an instructor
     *
     * @param int $instructorId
     * @return array Array of preset messages
     */
    public static function getPresets(int $instructorId): array
    {
        $filePath = self::getFilePath($instructorId);

        if (!Storage::exists($filePath)) {
            return [];
        }

        $contents = Storage::get($filePath);
        $data = json_decode($contents, true);

        if (!is_array($data) || !isset($data['presets']) || !is_array($data['presets'])) {
            return [];
        }

        return $data['presets'];
    }

    /**
     * Save preset messages for an instructor
     *
     * @param int $instructorId
     * @param array $presets Array of preset message strings
     * @return bool Success status
     */
    public static function savePresets(int $instructorId, array $presets): bool
    {
        // Ensure directory exists
        if (!Storage::exists(self::PRESETS_DIRECTORY)) {
            Storage::makeDirectory(self::PRESETS_DIRECTORY);
        }

        $data = [
            'instructor_id' => $instructorId,
            'updated_at' => now()->toIso8601String(),
            'presets' => array_values($presets), // Re-index array
        ];

        $filePath = self::getFilePath($instructorId);
        return Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Add a preset message for an instructor
     *
     * @param int $instructorId
     * @param string $message
     * @return bool Success status
     */
    public static function addPreset(int $instructorId, string $message): bool
    {
        $presets = self::getPresets($instructorId);
        $presets[] = trim($message);
        return self::savePresets($instructorId, $presets);
    }

    /**
     * Remove a preset message by index
     *
     * @param int $instructorId
     * @param int $index
     * @return bool Success status
     */
    public static function removePreset(int $instructorId, int $index): bool
    {
        $presets = self::getPresets($instructorId);

        if (!isset($presets[$index])) {
            return false;
        }

        unset($presets[$index]);
        return self::savePresets($instructorId, $presets);
    }

    /**
     * Update a preset message by index
     *
     * @param int $instructorId
     * @param int $index
     * @param string $newMessage
     * @return bool Success status
     */
    public static function updatePreset(int $instructorId, int $index, string $newMessage): bool
    {
        $presets = self::getPresets($instructorId);

        if (!isset($presets[$index])) {
            return false;
        }

        $presets[$index] = trim($newMessage);
        return self::savePresets($instructorId, $presets);
    }

    /**
     * Check if instructor has any presets
     *
     * @param int $instructorId
     * @return bool
     */
    public static function hasPresets(int $instructorId): bool
    {
        return count(self::getPresets($instructorId)) > 0;
    }

    /**
     * Delete all presets for an instructor
     *
     * @param int $instructorId
     * @return bool Success status
     */
    public static function deleteAllPresets(int $instructorId): bool
    {
        $filePath = self::getFilePath($instructorId);

        if (!Storage::exists($filePath)) {
            return true;
        }

        return Storage::delete($filePath);
    }
}
