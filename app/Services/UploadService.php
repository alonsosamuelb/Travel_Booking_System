<?php

namespace App\Services;

class UploadService
{
    public function storeTripImage(?array $file, ?string $currentPath = null): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Image upload failed.');
        }

        $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);

        if (!isset($allowedMime[$mime])) {
            throw new \RuntimeException('Only JPG, PNG and WEBP images are allowed.');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new \RuntimeException('Image size must be 2MB or lower.');
        }

        $directory = __DIR__ . '/../../public/uploads/trips';
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Trip upload directory could not be created.');
        }

        $filename = 'trip_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowedMime[$mime];
        $target = $directory . '/' . $filename;

        $optimized = $this->optimizeImage($file['tmp_name'], $target, $mime);
        if (!$optimized && !move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Uploaded image could not be stored.');
        }

        if ($currentPath && str_starts_with($currentPath, base_url('uploads/trips/'))) {
            $oldFile = __DIR__ . '/../../public/uploads/trips/' . basename($currentPath);
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        return base_url('uploads/trips/' . $filename);
    }

    private function optimizeImage(string $source, string $target, string $mime): bool
    {
        if (!function_exists('imagecreatetruecolor')) {
            return false;
        }

        $imageInfo = @getimagesize($source);
        if (!$imageInfo) {
            return false;
        }

        [$width, $height] = $imageInfo;
        if ($width < 1 || $height < 1) {
            return false;
        }

        $create = match ($mime) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : null,
            'image/png' => function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : null,
            'image/webp' => function_exists('imagecreatefromwebp') ? 'imagecreatefromwebp' : null,
            default => null,
        };

        if (!$create) {
            return false;
        }

        $sourceImage = @$create($source);
        if (!$sourceImage) {
            return false;
        }

        $maxWidth = 1400;
        $ratio = min(1, $maxWidth / $width);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));
        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($canvas, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $saved = match ($mime) {
            'image/jpeg' => imagejpeg($canvas, $target, 82),
            'image/png' => imagepng($canvas, $target, 7),
            'image/webp' => function_exists('imagewebp') ? imagewebp($canvas, $target, 80) : false,
            default => false,
        };

        imagedestroy($sourceImage);
        imagedestroy($canvas);

        return (bool) $saved;
    }
}
