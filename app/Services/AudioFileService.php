<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AudioFileService {
    public function sanitizeFileName(string $filePath) : string {
        $name = pathinfo($filePath, PATHINFO_FILENAME);
        if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $name)) {
            return $name;
        }

        Log::error("Security Alert: Unsafe filename detected. Original: {$filePath}");
        return false;
    }

    public function durationMs(string $filePath) : int {
        if (!$filePath || !file_exists($filePath)) {
            Log::warning("File not found for duration calculation: " . $filePath);
            return - 1;
        }

        $escapedFilePath = escapeshellarg($filePath);
        $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$escapedFilePath} 2>&1";

        $output = shell_exec($command);
        $duration = 0;

        $trimmedOutput = trim($output);

        if (is_numeric($trimmedOutput)) {
            $duration = (float) $trimmedOutput;
        } else {
            Log::error("FFprobe failed to get duration for {$filePath}. Output: " . $trimmedOutput);
        }

        return (int) ($duration * 1000);
    }

    public function convertToFlac(string $sourceFilePath, string $targetDirectory, string $outputFileName) : ?string {
        if (empty($sourceFilePath) || empty($targetDirectory) || empty($outputFileName)) {
            Log::error('Audio conversion failed: Source path, target directory, or output filename cannot be empty.');
            return null;
        }

        if (!file_exists($sourceFilePath)) {
            Log::error("Audio conversion failed: Source file not found at '{$sourceFilePath}'.");
            return null;
        }

        $sanitizedBaseName = pathinfo($outputFileName, PATHINFO_FILENAME);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $sanitizedBaseName)) {
            Log::error("Audio conversion failed: Unsafe characters detected in output filename '{$outputFileName}'.");
            return null;
        }

        $finalOutputFileName = $sanitizedBaseName . '.flac';
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true)) {
            Log::error("Audio conversion failed: Could not create target directory '{$targetDirectory}'.");
            return null;
        }
        
        if (!is_writable($targetDirectory)) {
            Log::error("Audio conversion failed: Target directory '{$targetDirectory}' is not writable.");
            return null;
        }

        $targetFilePath = rtrim($targetDirectory, '/').'/'.$finalOutputFileName;

        $escapedSource = escapeshellarg($sourceFilePath);
        $escapedTarget = escapeshellarg($targetFilePath);

        $command = "ffmpeg -i {$escapedSource} -compression_level 12 {$escapedTarget} 2>&1";

        $output = [];
        $returnVar = 0;

        exec($command, $output, $returnVar);

        $ffmpegOutput = implode("\n", $output);

        if ($returnVar !== 0) {
            Log::error("FFmpeg failed during audio conversion. Exit code: {$returnVar}. Command: '{$command}'. Output: {$ffmpegOutput}");
            return null;
        }

        if (!file_exists($targetFilePath)) {
            Log::error("FFmpeg completed, but target file not found after conversion: '{$targetFilePath}'. Output: {$ffmpegOutput}");
            return null;
        }

        Log::info("Audio converted successfully to '{$targetFilePath}'.");
        return $targetFilePath;
    }
}