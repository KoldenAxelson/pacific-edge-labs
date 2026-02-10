<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StorageService
{
    /**
     * Upload a CoA PDF and return the path
     *
     * @param UploadedFile $file
     * @param string $batchNumber
     * @return string The S3 path to the uploaded file
     */
    public function uploadCoa(UploadedFile $file, string $batchNumber): string
    {
        $filename = "coa-{$batchNumber}.pdf";
        $path = "coas/{$filename}";
        
        Storage::disk('coas')->putFileAs('coas', $file, $filename);
        
        return $path;
    }

    /**
     * Get a temporary signed URL for a CoA
     *
     * @param string $path The S3 path to the CoA file
     * @param int $minutes Number of minutes the URL should be valid
     * @return string Temporary signed URL
     */
    public function getCoaUrl(string $path, int $minutes = 60): string
    {
        return Storage::disk('coas')->temporaryUrl($path, now()->addMinutes($minutes));
    }

    /**
     * Upload a product image and return the public URL
     *
     * @param UploadedFile $file
     * @param string $productSku
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
     * Delete a CoA (soft delete via versioning)
     * Note: With versioning enabled, files are never truly deleted
     *
     * @param string $path The S3 path to the CoA file
     * @return bool
     */
    public function deleteCoa(string $path): bool
    {
        return Storage::disk('coas')->delete($path);
    }

    /**
     * Delete a product image
     *
     * @param string $url The public URL of the image to delete
     * @return bool
     */
    public function deleteProductImage(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        
        return Storage::disk('products')->delete($path);
    }

    /**
     * Check if a CoA exists
     *
     * @param string $path
     * @return bool
     */
    public function coaExists(string $path): bool
    {
        return Storage::disk('coas')->exists($path);
    }

    /**
     * Check if a product image exists by URL
     *
     * @param string $url
     * @return bool
     */
    public function productImageExists(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        
        return Storage::disk('products')->exists($path);
    }

    /**
     * Get all CoAs in a directory
     *
     * @param string $directory
     * @return array
     */
    public function listCoas(string $directory = 'coas'): array
    {
        return Storage::disk('coas')->files($directory);
    }

    /**
     * Get all product images in a directory
     *
     * @param string $directory
     * @return array
     */
    public function listProductImages(string $directory = 'products'): array
    {
        return Storage::disk('products')->files($directory);
    }
}
