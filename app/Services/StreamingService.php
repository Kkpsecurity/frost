<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class StreamingService
{
    /**
     * Stream file with basic headers
     */
    public function streamFile(MediaFile $file, array $headers = []): StreamedResponse
    {
        $disk = Storage::disk($file->disk);

        if (!$disk->exists($file->path)) {
            abort(404);
        }

        $size = $disk->size($file->path);
        $mimeType = $file->mime_type;

        return response()->stream(function () use ($disk, $file) {
            $stream = $disk->readStream($file->path);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, array_merge([
            'Content-Type' => $mimeType,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, must-revalidate',
        ], $headers));
    }

    /**
     * Stream file with HTTP range support for video/audio
     */
    public function streamWithRange(MediaFile $file, string $range = null): StreamedResponse
    {
        $disk = Storage::disk($file->disk);
        $size = $disk->size($file->path);

        if ($range && $this->isRangeSupported($file)) {
            return $this->handleRangeRequest($file, $range, $size);
        }

        return $this->streamFile($file);
    }

    /**
     * Check if file type supports range requests
     */
    private function isRangeSupported(MediaFile $file): bool
    {
        return $file->isVideo() || $file->isAudio();
    }

    /**
     * Handle HTTP range request for partial content
     */
    private function handleRangeRequest(MediaFile $file, string $range, int $size): StreamedResponse
    {
        // Parse range header: "bytes=start-end"
        if (!preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
            return $this->streamFile($file);
        }

        $start = (int) $matches[1];
        $end = $matches[2] !== '' ? (int) $matches[2] : $size - 1;

        // Validate range
        if ($start >= $size || $end >= $size || $start > $end) {
            return response()->stream(function() {}, 416, [
                'Content-Range' => "bytes */{$size}"
            ]);
        }

        $length = $end - $start + 1;
        $disk = Storage::disk($file->disk);

        return response()->stream(function () use ($disk, $file, $start, $length) {
            $stream = $disk->readStream($file->path);
            if ($stream) {
                fseek($stream, $start);
                echo fread($stream, $length);
                fclose($stream);
            }
        }, 206, [
            'Content-Type' => $file->mime_type,
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$size}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
