# [TASK-0-004] S3 Bucket Creation & Laravel Filesystem Configuration

## Overview
Create AWS S3 buckets for storing Certificate of Analysis (CoA) PDFs and product images, then configure Laravel's filesystem to use S3 for these file types.

## Prerequisites
- [x] AWS CLI installed and configured
- [x] AWS account with S3 access
- [x] TASK-0-001 completed (Laravel project initialized)

## Goals
- Create two S3 buckets: `pacific-edge-coas` and `pacific-edge-products`
- Configure bucket policies (private for CoAs, public-read for product images)
- Install and configure AWS SDK for PHP in Laravel
- Set up Laravel filesystem disks for both buckets
- Test file upload/download to both buckets

## Step-by-Step Instructions

### 1. Verify AWS CLI Configuration

```bash
aws configure list
```

You should see your credentials configured. If not:

```bash
aws configure
```

Enter:
- AWS Access Key ID
- AWS Secret Access Key
- Default region: `us-west-2`
- Default output format: `json`

### 2. Create S3 Bucket for CoA PDFs (Private)

```bash
aws s3api create-bucket \
    --bucket pacific-edge-coas \
    --region us-west-2 \
    --create-bucket-configuration LocationConstraint=us-west-2
```

### 3. Enable Versioning on CoA Bucket (Compliance Requirement)

```bash
aws s3api put-bucket-versioning \
    --bucket pacific-edge-coas \
    --versioning-configuration Status=Enabled
```

This ensures CoAs can never be permanently deleted (audit trail).

### 4. Block Public Access on CoA Bucket

```bash
aws s3api put-public-access-block \
    --bucket pacific-edge-coas \
    --public-access-block-configuration \
        "BlockPublicAcls=true,IgnorePublicAcls=true,BlockPublicPolicy=true,RestrictPublicBuckets=true"
```

CoAs must be private - only authenticated users can access them.

### 5. Create S3 Bucket for Product Images (Public-Read)

```bash
aws s3api create-bucket \
    --bucket pacific-edge-products \
    --region us-west-2 \
    --create-bucket-configuration LocationConstraint=us-west-2
```

### 6. Configure Public-Read Access for Product Images

Create a bucket policy file:

```bash
cat > /tmp/product-bucket-policy.json << 'EOF'
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::pacific-edge-products/*"
        }
    ]
}
EOF
```

Apply the policy:

```bash
aws s3api put-bucket-policy \
    --bucket pacific-edge-products \
    --policy file:///tmp/product-bucket-policy.json
```

### 7. Enable CORS for Product Images

Create CORS configuration:

```bash
cat > /tmp/cors-config.json << 'EOF'
{
    "CORSRules": [
        {
            "AllowedOrigins": ["*"],
            "AllowedMethods": ["GET", "HEAD"],
            "AllowedHeaders": ["*"],
            "MaxAgeSeconds": 3000
        }
    ]
}
EOF
```

Apply CORS:

```bash
aws s3api put-bucket-cors \
    --bucket pacific-edge-products \
    --cors-configuration file:///tmp/cors-config.json
```

### 8. Install AWS SDK for PHP

```bash
sail composer require league/flysystem-aws-s3-v3 "^3.0"
```

This installs the S3 adapter for Laravel's filesystem.

### 9. Configure Environment Variables

Edit `.env`:

```env
AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=pacific-edge-coas
AWS_USE_PATH_STYLE_ENDPOINT=false

# Additional buckets
AWS_COA_BUCKET=pacific-edge-coas
AWS_PRODUCTS_BUCKET=pacific-edge-products
```

Replace `your_access_key_here` and `your_secret_key_here` with actual credentials.

### 10. Update `.env.example`

Edit `.env.example` (remove actual credentials):

```env
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=pacific-edge-coas
AWS_USE_PATH_STYLE_ENDPOINT=false

AWS_COA_BUCKET=pacific-edge-coas
AWS_PRODUCTS_BUCKET=pacific-edge-products
```

### 11. Configure Laravel Filesystem Disks

Edit `config/filesystems.php`:

```php
<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        // CoA PDFs - Private bucket
        'coas' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_COA_BUCKET', 'pacific-edge-coas'),
            'visibility' => 'private',
            'throw' => false,
        ],

        // Product images - Public bucket
        'products' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_PRODUCTS_BUCKET', 'pacific-edge-products'),
            'visibility' => 'public',
            'url' => 'https://pacific-edge-products.s3.us-west-2.amazonaws.com',
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
```

### 12. Create Test Route for S3 Upload

Edit `routes/web.php` and add:

```php
// S3 Upload Test Route (REMOVE THIS IN PRODUCTION)
Route::get('/test-s3', function () {
    $results = [];
    
    try {
        // Test CoA bucket (private)
        Storage::disk('coas')->put('test-coa.txt', 'This is a test CoA file.');
        $coaExists = Storage::disk('coas')->exists('test-coa.txt');
        $results['coa_upload'] = $coaExists ? '✅ Success' : '❌ Failed';
        
        // Test getting a temporary URL for CoA (signed URL)
        $coaUrl = Storage::disk('coas')->temporaryUrl('test-coa.txt', now()->addMinutes(5));
        $results['coa_signed_url'] = $coaUrl;
        
        // Test Product bucket (public)
        Storage::disk('products')->put('test-product.txt', 'This is a test product image.');
        $productExists = Storage::disk('products')->exists('test-product.txt');
        $results['product_upload'] = $productExists ? '✅ Success' : '❌ Failed';
        
        // Test getting public URL for product
        $productUrl = Storage::disk('products')->url('test-product.txt');
        $results['product_public_url'] = $productUrl;
        
    } catch (\Exception $e) {
        $results['error'] = $e->getMessage();
    }
    
    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
})->name('test-s3');
```

### 13. Test S3 Upload

Visit: http://localhost/test-s3

You should see JSON output like:

```json
{
    "coa_upload": "✅ Success",
    "coa_signed_url": "https://pacific-edge-coas.s3.us-west-2.amazonaws.com/test-coa.txt?X-Amz-...",
    "product_upload": "✅ Success",
    "product_public_url": "https://pacific-edge-products.s3.us-west-2.amazonaws.com/test-product.txt"
}
```

### 14. Verify Files in S3

```bash
# Check CoA bucket
aws s3 ls s3://pacific-edge-coas/

# Check Products bucket
aws s3 ls s3://pacific-edge-products/
```

You should see `test-coa.txt` and `test-product.txt`.

### 15. Test Signed URL Access

Copy the `coa_signed_url` from the test output and paste it into a browser. You should be able to download the file.

The URL will expire after 5 minutes (as configured).

### 16. Test Public URL Access

Copy the `product_public_url` and paste it into a browser. You should see the file content.

This URL does NOT expire (public-read).

### 17. Clean Up Test Files

```bash
aws s3 rm s3://pacific-edge-coas/test-coa.txt
aws s3 rm s3://pacific-edge-products/test-product.txt
```

Or keep them for reference - they're harmless.

### 18. Document S3 Bucket Costs

Create `docs/aws-resources.md`:

```bash
mkdir -p docs
cat > docs/aws-resources.md << 'EOF'
# AWS Resources - Pacific Edge Labs

## S3 Buckets

### pacific-edge-coas (Private)
- **Purpose:** Certificate of Analysis PDF storage
- **Region:** us-west-2
- **Visibility:** Private (signed URLs only)
- **Versioning:** Enabled (compliance requirement)
- **Estimated Cost:** $0.023/GB/month + $0.0004/1000 GET requests
- **Expected Usage:** ~100 PDFs (~50MB total) = **~$0.01/month**

### pacific-edge-products (Public-Read)
- **Purpose:** Product images
- **Region:** us-west-2
- **Visibility:** Public-read
- **Versioning:** Disabled
- **Estimated Cost:** $0.023/GB/month + $0.0004/1000 GET requests
- **Expected Usage:** ~500 images (~2GB total) = **~$0.05/month**

## Total Monthly Cost Estimate
**S3:** ~$0.06/month (negligible during demo phase)

## Notes
- Both buckets created in development AWS account
- Will be recreated in client AWS account during Phase 8 handoff
- All S3 operations are abstracted through Laravel Storage facade
EOF
```

### 19. Create S3 Helper Service (Optional but Recommended)

```bash
sail artisan make:class Services/StorageService
```

Edit `app/Services/StorageService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StorageService
{
    /**
     * Upload a CoA PDF and return the path
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
     */
    public function getCoaUrl(string $path, int $minutes = 60): string
    {
        return Storage::disk('coas')->temporaryUrl($path, now()->addMinutes($minutes));
    }

    /**
     * Upload a product image and return the public URL
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
     */
    public function deleteCoa(string $path): bool
    {
        return Storage::disk('coas')->delete($path);
    }

    /**
     * Delete a product image
     */
    public function deleteProductImage(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        
        return Storage::disk('products')->delete($path);
    }
}
```

### 20. Register Service in AppServiceProvider (Optional)

If you want to use dependency injection, register it in `app/Providers/AppServiceProvider.php`:

```php
use App\Services\StorageService;

public function register(): void
{
    $this->app->singleton(StorageService::class);
}
```

### 21. Commit Changes

```bash
git add .
git commit -m "Configure S3 buckets for CoAs and product images"
git push
```

## Validation Checklist

- [ ] `aws s3 ls` shows both buckets created
- [ ] CoA bucket has versioning enabled
- [ ] CoA bucket blocks all public access
- [ ] Product bucket allows public-read
- [ ] http://localhost/test-s3 returns success for both uploads
- [ ] Signed URL for CoA works and expires after 5 minutes
- [ ] Public URL for product image works and doesn't expire
- [ ] AWS credentials in `.env` (NOT committed to Git)
- [ ] `docs/aws-resources.md` documents bucket details
- [ ] Test files cleaned up (optional)

## Common Issues & Solutions

### Issue: "The specified bucket does not exist"
**Solution:**
Verify bucket was created:
```bash
aws s3 ls
```

Check region matches:
```bash
aws s3api get-bucket-location --bucket pacific-edge-coas
```

### Issue: "Access Denied" when uploading
**Solution:**
Verify AWS credentials have S3 write permissions:
```bash
aws iam get-user
```

Check IAM policy includes `s3:PutObject` permission.

### Issue: Public URL returns 403 Forbidden
**Solution:**
Verify bucket policy allows public reads:
```bash
aws s3api get-bucket-policy --bucket pacific-edge-products
```

### Issue: Signed URL doesn't work
**Solution:**
Check system time is synchronized (signed URLs are time-sensitive):
```bash
date
```

### Issue: CORS errors in browser
**Solution:**
Verify CORS configuration:
```bash
aws s3api get-bucket-cors --bucket pacific-edge-products
```

## Security Best Practices

1. **Never commit AWS credentials to Git**
   - Always use `.env` file
   - Add `.env` to `.gitignore` (already done by Laravel)

2. **Use IAM roles in production**
   - Lightsail instance should use IAM role, not hardcoded keys
   - We'll configure this in TASK-0-008

3. **Encrypt CoAs at rest** (optional for Phase 0)
   ```bash
   aws s3api put-bucket-encryption \
       --bucket pacific-edge-coas \
       --server-side-encryption-configuration \
       '{"Rules":[{"ApplyServerSideEncryptionByDefault":{"SSEAlgorithm":"AES256"}}]}'
   ```

4. **Enable S3 access logging** (deferred to Phase 7)

## File Upload Size Limits

Default PHP/Laravel limits:
- `upload_max_filesize`: 2MB
- `post_max_size`: 8MB

For production, update these in Lightsail (TASK-0-008):
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-004 as complete
- ➡️ Proceed to TASK-0-005 (Filament Installation)

## Time Estimate
**30-45 minutes**

## Success Criteria
- Two S3 buckets created and configured correctly
- Laravel filesystem disks configured for both buckets
- Test uploads successful to both buckets
- Signed URLs working for private CoAs
- Public URLs working for product images
- AWS resources documented
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-001  
**Blocks:** TASK-0-005 (Filament may need S3 for admin uploads)  
**Priority:** High
