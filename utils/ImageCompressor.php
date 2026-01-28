<?php
/**
 * ImageCompressor - Automatic Image Compression Utility
 * 
 * Features:
 * - Resize images to max width (default 1200px)
 * - Convert to WebP format
 * - Iterative quality adjustment to achieve target file size
 * - Support JPEG, PNG, GIF input formats
 */

class ImageCompressor {
    
    private $maxWidth;
    private $targetSize;
    private $minQuality = 60;
    private $maxQuality = 85;
    
    /**
     * Constructor
     * 
     * @param int $maxWidth Maximum width in pixels (default: 1200)
     * @param int $targetSize Target file size in bytes (default: 500KB)
     */
    public function __construct($maxWidth = 1200, $targetSize = 512000) {
        $this->maxWidth = $maxWidth;
        $this->targetSize = $targetSize;
    }
    
    /**
     * Compress and convert image to WebP
     * 
     * @param string $sourcePath Path to source image
     * @param string $targetPath Path to save WebP image
     * @return array ['success' => bool, 'message' => string, 'size' => int, 'width' => int, 'height' => int]
     */
    public function compress($sourcePath, $targetPath) {
        try {
            // Check if source file exists
            if (!file_exists($sourcePath)) {
                return [
                    'success' => false,
                    'message' => 'Source file not found'
                ];
            }
            
            // Get image info
            $imageInfo = getimagesize($sourcePath);
            if ($imageInfo === false) {
                return [
                    'success' => false,
                    'message' => 'Invalid image file'
                ];
            }
            
            // Load image based on type
            $image = $this->loadImage($sourcePath, $imageInfo[2]);
            if ($image === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to load image'
                ];
            }
            
            // Resize if needed
            $resized = $this->resizeImage($image, $imageInfo[0], $imageInfo[1]);
            
            // Get optimal quality for target size
            $quality = $this->getOptimalQuality($resized, $targetPath);
            
            // Save as WebP
            $success = imagewebp($resized, $targetPath, $quality);
            
            // Clean up
            imagedestroy($image);
            if ($resized !== $image) {
                imagedestroy($resized);
            }
            
            if (!$success) {
                return [
                    'success' => false,
                    'message' => 'Failed to save WebP image'
                ];
            }
            
            // Get final file info
            $finalSize = filesize($targetPath);
            $finalInfo = getimagesize($targetPath);
            
            return [
                'success' => true,
                'message' => 'Image compressed successfully',
                'size' => $finalSize,
                'width' => $finalInfo[0],
                'height' => $finalInfo[1],
                'quality' => $quality,
                'format' => 'WebP'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Compression error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Load image from file based on type
     * 
     * @param string $path Image path
     * @param int $type Image type constant
     * @return resource|false GD image resource or false on failure
     */
    private function loadImage($path, $type) {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }
    
    /**
     * Resize image to max width while maintaining aspect ratio
     * 
     * @param resource $image GD image resource
     * @param int $currentWidth Current width
     * @param int $currentHeight Current height
     * @return resource Resized GD image resource
     */
    private function resizeImage($image, $currentWidth, $currentHeight) {
        // If image is smaller than max width, no resize needed
        if ($currentWidth <= $this->maxWidth) {
            return $image;
        }
        
        // Calculate new dimensions
        $ratio = $this->maxWidth / $currentWidth;
        $newWidth = $this->maxWidth;
        $newHeight = (int)($currentHeight * $ratio);
        
        // Create new image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG/GIF
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        // Resize
        imagecopyresampled(
            $resized, $image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $currentWidth, $currentHeight
        );
        
        return $resized;
    }
    
    /**
     * Find optimal quality to achieve target file size
     * Uses iterative approach to find best quality
     * 
     * @param resource $image GD image resource
     * @param string $targetPath Target file path
     * @return int Optimal quality value
     */
    private function getOptimalQuality($image, $targetPath) {
        $quality = $this->maxQuality;
        $tempPath = $targetPath . '.tmp';
        
        // Try with max quality first
        imagewebp($image, $tempPath, $quality);
        $size = filesize($tempPath);
        
        // If already under target, use max quality
        if ($size <= $this->targetSize) {
            unlink($tempPath);
            return $quality;
        }
        
        // Binary search for optimal quality
        $low = $this->minQuality;
        $high = $this->maxQuality;
        $bestQuality = $this->minQuality;
        
        while ($low <= $high) {
            $quality = (int)(($low + $high) / 2);
            
            imagewebp($image, $tempPath, $quality);
            $size = filesize($tempPath);
            
            if ($size <= $this->targetSize) {
                $bestQuality = $quality;
                $low = $quality + 1; // Try higher quality
            } else {
                $high = $quality - 1; // Try lower quality
            }
        }
        
        unlink($tempPath);
        return $bestQuality;
    }
    
    /**
     * Format file size for display
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted size (e.g., "245 KB")
     */
    public static function formatSize($bytes) {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
