<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * Storage service providing abstraction layer for S3-based file operations.
 *
 * Manages uploads, retrieval, and deletion of certificates of analysis (CoAs) and product
 * images across separate S3 buckets. Provides secure temporary URLs for CoA access, handles
 * file naming conventions, and abstracts away cloud storage implementation details. Supports
 * S3 versioning for compliance auditing and soft deletes.
 */
class StorageService
{
    /**
     * Upload a CoA PDF and return the S3 storage path.
     *
     * @param UploadedFile $file The PDF file to upload
     * @param string $batchNumber The batch number used to name the file (e.g., 'PEL-2025-0142')
     * @return string The S3 path to the uploaded file (e.g., 'coas/coa-PEL-2025-0142.pdf')
     */
    public function uploadCoa(UploadedFile $file, string $batchNumber): string
    {
        $filename = "coa-{$batchNumber}.pdf";
        $path = "coas/{$filename}";

        Storage::disk('coas')->putFileAs('coas', $file, $filename);

        return $path;
    }

    /**
     * Get a temporary signed URL for a CoA.
     *
     * @param string $path The S3 path to the CoA file
     * @param int $minutes Number of minutes the URL should be valid (default 60)
     * @return string Temporary signed URL for secure download
     */
    public function getCoaUrl(string $path, int $minutes = 60): string
    {
        return Storage::disk('coas')->temporaryUrl($path, now()->addMinutes($minutes));
    }

    /**
     * Upload a product image and return the public URL.
     *
     * @param UploadedFile $file The image file to upload
     * @param string $productSku The product SKU used to name the file (e.g., 'PEL-SEM-15')
     * @return string Public URL to the uploaded image
     */
    public function uploadProductImage(UploadedFile $file, string $productSku): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "{$productSku}-" . time() . ".{$extension}";
        $path = "products/{$filename}";

        Storage::disk('products')->putFileAs('products', $file, $filename);

        return Storage::disk('products')->url($path);
    }

    /**
     * Delete a CoA file from S3.
     *
     * With S3 versioning enabled, files are soft-deleted and can be recovered
     * for compliance auditing purposes.
     *
     * @param string $path The S3 path to the CoA file
     * @return bool True if deletion was successful
     */
    public function deleteCoa(string $path): bool
    {
        return Storage::disk('coas')->delete($path);
    }

    /**
     * Delete a product image from S3.
     *
     * @param string $url The public URL of the image to delete
     * @return bool True if deletion was successful
     */
    public function deleteProductImage(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');

        return Storage::disk('products')->delete($path);
    }

    /**
     * Check if a CoA file exists in S3.
     *
     * @param string $path The S3 path to check
     * @return bool True if the file exists
     */
    public function coaExists(string $path): bool
    {
        return Storage::disk('coas')->exists($path);
    }

    /**
     * Check if a product image exists in S3 by its public URL.
     *
     * @param string $url The public URL of the image to check
     * @return bool True if the image exists
     */
    public function productImageExists(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');

        return Storage::disk('products')->exists($path);
    }

    /**
     * List all CoA files in an S3 directory.
     *
     * @param string $directory The directory to list (default 'coas')
     * @return array<string> Array of file paths
     */
    public function listCoas(string $directory = 'coas'): array
    {
        return Storage::disk('coas')->files($directory);
    }

    /**
     * List all product images in an S3 directory.
     *
     * @param string $directory The directory to list (default 'products')
     * @return array<string> Array of file paths
     */
    public function listProductImages(string $directory = 'products'): array
    {
        return Storage::disk('products')->files($directory);
    }
}
