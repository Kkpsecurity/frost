<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Text Processing Toolkit - Comprehensive text manipulation and formatting utilities
 *
 * Provides secure text sanitization, formatting, encoding, and conversion functions
 * for web applications with focus on security and data integrity.
 */
class TextTk
{
    // =====================================
    // Sanitization Flags
    // =====================================

    public const SANITIZE_NO_TRIM = 0x1;
    public const SANITIZE_NO_STRIPTAGS = 0x2;
    public const SANITIZE_NO_DOS2UNIX = 0x4;


    // =====================================
    // Text Sanitization
    // =====================================

    /**
     * Sanitize text with configurable options
     *
     * Performs comprehensive text cleaning including:
     * - HTML tag stripping
     * - Whitespace trimming
     * - DOS to UNIX line ending conversion
     * - Smart quote replacement
     *
     * @param string|null $str Input string to sanitize
     * @param int $flags Bitwise flags to disable specific behaviors
     * @return string|null Sanitized string or null if empty
     */
    public static function sanitize(string|null $str, int $flags = 0): string|null
    {
        // Return null for empty input (database compatibility)
        if ($str === null || $str === '') {
            return null;
        }

        // Strip HTML tags unless disabled
        if (!($flags & self::SANITIZE_NO_STRIPTAGS)) {
            $str = strip_tags($str);
        }

        // Trim whitespace unless disabled
        if (!($flags & self::SANITIZE_NO_TRIM)) {
            $str = trim($str);
        }

        // Convert DOS line endings unless disabled
        if (!($flags & self::SANITIZE_NO_DOS2UNIX)) {
            $str = str_replace("\r\n", "\n", $str);
        }

        // Always strip smart quotes
        $str = self::stripSmartQuotes($str);

        // Return null if result is empty
        return $str !== '' ? $str : null;
    }

    /**
     * Replace Microsoft "Smart Quotes" and special characters with standard equivalents
     *
     * Handles both UTF-8 and Windows-1252 encoded smart quotes, em-dashes,
     * ellipsis, and middle dot characters for consistent text display.
     *
     * @param string $str Input string with potential smart quotes
     * @return string String with smart quotes replaced
     */
    public static function stripSmartQuotes(string $str): string
    {
        // Replace UTF-8 smart quotes and special characters
        $utf8_replacements = [
            "\xe2\x80\x98" => "'",     // Left single quotation mark
            "\xe2\x80\x99" => "'",     // Right single quotation mark
            "\xe2\x80\x9c" => '"',     // Left double quotation mark
            "\xe2\x80\x9d" => '"',     // Right double quotation mark
            "\xe2\x80\x93" => '-',     // En dash
            "\xe2\x80\x94" => '-',     // Em dash
            "\xe2\x80\xa6" => '...',   // Horizontal ellipsis
        ];

        $str = str_replace(array_keys($utf8_replacements), array_values($utf8_replacements), $str);

        // Replace Windows-1252 equivalents
        $windows_replacements = [
            chr(145) => "'",     // Left single quotation mark
            chr(146) => "'",     // Right single quotation mark
            chr(147) => '"',     // Left double quotation mark
            chr(148) => '"',     // Right double quotation mark
            chr(150) => '-',     // En dash
            chr(151) => '-',     // Em dash
            chr(133) => '...',   // Horizontal ellipsis
            chr(183) => '&#183;' // Middle dot (preserve as HTML entity)
        ];

        return str_replace(array_keys($windows_replacements), array_values($windows_replacements), $str);
    }

    // =====================================
    // Text Encoding & Escaping
    // =====================================

    /**
     * Escape string for safe JavaScript usage
     *
     * Properly escapes characters that could break JavaScript strings
     * including quotes, newlines, and HTML-sensitive characters.
     *
     * @param string $str Input string to escape
     * @return string JavaScript-safe escaped string
     */
    public static function addJsSlashes(string $str): string
    {
        $replacements = [
            '\\' => '\\\\',    // Backslash
            "\n" => '\\n',     // Newline
            "\r" => '\\r',     // Carriage return
            '"'  => '\\"',     // Double quote
            "'"  => "\\'",     // Single quote
            '&'  => '\\x26',   // Ampersand
            '<'  => '\\x3C',   // Less than
            '>'  => '\\x3E'    // Greater than
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $str);
    }

    /**
     * Format plain text for HTML display
     *
     * Converts newlines to <br /> tags and preserves spacing
     * by converting double spaces to non-breaking spaces.
     *
     * @param string $str Input text to format
     * @return string HTML-formatted text
     */
    public static function formatBlockText(string $str): string
    {
        // Remove legacy slashes and clean smart quotes
        $str = stripslashes($str);
        $str = self::stripSmartQuotes($str);

        // Convert formatting
        return str_replace(
            ["\n", '  '],
            ['<br />', '&nbsp;&nbsp;'],
            $str
        );
    }


    // =====================================
    // Data Formatting
    // =====================================

    /**
     * Format 10-digit telephone number
     *
     * Formats a string of 10 digits into readable phone number format.
     * Supports both space and dot separators.
     *
     * @param string $str 10-digit phone number string
     * @param bool $dots Use dots instead of spaces as separators
     * @return string Formatted phone number (XXX.XXX.XXXX or XXX XXX XXXX)
     * @throws \InvalidArgumentException If string is not 10 digits
     */
    public static function formatTelephone(string $str, bool $dots = false): string
    {
        // Validate input length
        if (strlen($str) !== 10) {
            throw new \InvalidArgumentException('Phone number must be exactly 10 digits');
        }

        // Validate all characters are digits
        if (!ctype_digit($str)) {
            throw new \InvalidArgumentException('Phone number must contain only digits');
        }

        $separator = $dots ? '.' : ' ';

        return substr($str, 0, 3) . $separator
             . substr($str, 3, 3) . $separator
             . substr($str, 6, 4);
    }

    /**
     * Convert bytes to human-readable string
     *
     * Automatically selects appropriate unit (B, kB, MB, GB, etc.)
     * and formats with specified precision.
     *
     * @param int|float $size Size in bytes
     * @param int $precision Number of decimal places
     * @return string Formatted size string (e.g., "1.5 GB")
     */
    public static function bytesToString(int|float $size, int $precision = 0): string
    {
        if ($size < 0) {
            throw new \InvalidArgumentException('Size cannot be negative');
        }

        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, $precision) . ' ' . $units[$unitIndex];
    }

    /**
     * Convert seconds to HH:MM:SS format
     *
     * Formats duration in seconds to readable time format.
     * Optionally omits hours if zero for shorter display.
     *
     * @param int $seconds Duration in seconds
     * @param bool $skipEmptyHours Omit "00:" hours prefix if zero
     * @return string Formatted time string
     * @throws \InvalidArgumentException If seconds is negative
     */
    public static function hms(int $seconds, bool $skipEmptyHours = false): string
    {
        if ($seconds < 0) {
            throw new \InvalidArgumentException('Seconds cannot be negative');
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        $timeStr = sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);

        if ($skipEmptyHours && $hours === 0) {
            $timeStr = substr($timeStr, 3); // Remove "00:" prefix
        }

        return $timeStr;
    }


    // =====================================
    // Base64 URL Encoding
    // =====================================

    /**
     * Encode string using Base64 URL-safe format
     *
     * Creates URL-safe Base64 encoding by replacing problematic characters
     * and removing padding. Safe for use in URLs and file names.
     *
     * @param string $string Input string to encode
     * @return string URL-safe Base64 encoded string
     */
    public static function base64EncodeUrl(string $string): string
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($string)
        );
    }

    /**
     * Decode Base64 URL-safe encoded string
     *
     * Converts URL-safe Base64 back to original string by restoring
     * standard Base64 characters before decoding.
     *
     * @param string $string URL-safe Base64 encoded string
     * @return string|false Decoded string or false on failure
     */
    public static function base64DecodeUrl(string $string): string|false
    {
        // Restore standard Base64 characters
        $standardBase64 = str_replace(['-', '_'], ['+', '/'], $string);

        // Add padding if needed
        $padLength = 4 - (strlen($standardBase64) % 4);
        if ($padLength !== 4) {
            $standardBase64 .= str_repeat('=', $padLength);
        }

        return base64_decode($standardBase64, true);
    }
}
