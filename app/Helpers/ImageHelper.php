<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Resizes and crops an image to the exact target width and height proportionally.
     * Keeps the original format.
     */
    public static function resizeAndCrop(string $filePath, int $targetWidth, int $targetHeight): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        // Get image details
        $imageInfo = @getimagesize($filePath);
        if (!$imageInfo) {
            return false;
        }

        $srcWidth = $imageInfo[0];
        $srcHeight = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Load image based on mime type
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = @imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $srcImage = @imagecreatefromgif($filePath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($filePath);
                break;
            default:
                return false;
        }

        if (!$srcImage) {
            return false;
        }

        // Calculate aspect ratios
        $srcRatio = $srcWidth / $srcHeight;
        $targetRatio = $targetWidth / $targetHeight;

        // Calculate scaled dimensions and offsets for cropping
        if ($srcRatio > $targetRatio) {
            // Source is wider than target. Scale by height, crop left and right.
            $srcCropWidth = (int) round($targetWidth * ($srcHeight / $targetHeight));
            $srcCropHeight = $srcHeight;
            $srcX = (int) round(($srcWidth - $srcCropWidth) / 2);
            $srcY = 0;
        } else {
            // Source is taller than target. Scale by width, crop top and bottom.
            $srcCropWidth = $srcWidth;
            $srcCropHeight = (int) round($targetHeight * ($srcWidth / $targetWidth));
            $srcX = 0;
            $srcY = (int) round(($srcHeight - $srcCropHeight) / 2);
        }

        // Create new blank true color image
        $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG and WebP
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }

        // Resample and crop
        $success = imagecopyresampled(
            $dstImage,
            $srcImage,
            0, 0, // Destination X, Y
            $srcX, $srcY, // Source X, Y (starts cropping from calculated offsets)
            $targetWidth, $targetHeight, // Destination width, height
            $srcCropWidth, $srcCropHeight // Source width, height to crop
        );

        if ($success) {
            // Save image back in original format
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($dstImage, $filePath, 90);
                    break;
                case 'image/png':
                    imagepng($dstImage, $filePath, 9);
                    break;
                case 'image/gif':
                    imagegif($dstImage, $filePath);
                    break;
                case 'image/webp':
                    imagewebp($dstImage, $filePath, 85);
                    break;
            }
        }

        // Destroy images in memory
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $success;
    }
}
