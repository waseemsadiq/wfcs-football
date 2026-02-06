<?php

declare(strict_types=1);

namespace Core;

/**
 * Upload helper for secure file handling.
 */
class Upload
{
    /**
     * Upload a file to the specified directory.
     *
     * @param array $file The $_FILES array element
     * @param string $directory Target directory (relative to uploads/)
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes (default 5MB)
     * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
     */
    public static function uploadFile(
        array $file,
        string $directory,
        array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'],
        int $maxSize = 5242880  // 5MB
    ): array {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'filename' => null,
                'error' => self::getUploadError($file['error']),
            ];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'filename' => null,
                'error' => 'File size exceeds maximum allowed (' . self::formatBytes($maxSize) . ')',
            ];
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return [
                'success' => false,
                'filename' => null,
                'error' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes),
            ];
        }

        // Create target directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/' . trim($directory, '/');
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return [
                    'success' => false,
                    'filename' => null,
                    'error' => 'Failed to create upload directory',
                ];
            }
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = self::generateUniqueFilename($extension);
        $targetPath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'success' => false,
                'filename' => null,
                'error' => 'Failed to move uploaded file',
            ];
        }

        return [
            'success' => true,
            'filename' => $filename,
            'error' => null,
        ];
    }

    /**
     * Delete a file from the uploads directory.
     *
     * @param string $directory Directory (relative to uploads/)
     * @param string $filename Filename to delete
     * @return bool Success status
     */
    public static function deleteFile(string $directory, string $filename): bool
    {
        $filePath = __DIR__ . '/../uploads/' . trim($directory, '/') . '/' . $filename;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Generate a unique filename.
     *
     * @param string $extension File extension
     * @return string Unique filename
     */
    private static function generateUniqueFilename(string $extension): string
    {
        return uniqid('fixture_', true) . '.' . strtolower($extension);
    }

    /**
     * Get human-readable upload error message.
     *
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private static function getUploadError(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File is too large',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            default => 'Unknown upload error',
        };
    }

    /**
     * Format bytes into human-readable size.
     *
     * @param int $bytes Bytes
     * @return string Formatted size
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
