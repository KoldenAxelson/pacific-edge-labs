# [TASK-0-007] Payment Abstraction Layer Skeleton

## Overview
Create a payment processor abstraction layer that allows swapping payment gateways without changing business logic. Start with a mock gateway for development, with architecture ready for real processors in production.

## Prerequisites
- [x] TASK-0-001 completed (Laravel project initialized)

## Goals
- Create payment gateway interface for abstraction
- Implement mock payment gateway for development
- Create payment transaction logging system
- Document payment gateway architecture
- Prepare for production gateway integration (Phase 7)

## Important Context

**Why This Matters:**  
Payment processors for peptide/research chemical businesses are EXTREMELY fragile. Many processors will drop you without warning. Having an abstraction layer means we can swap processors in minutes, not weeks.

**Production Options (Phase 7):**
- Authorize.net (most stable for this industry)
- Square (backup option)
- PayPal (high fees but accepted)
- Crypto payments (USDC - future enhancement)

## Step-by-Step Instructions

### 1. Create Payment Gateway Interface

```bash
mkdir -p app/Contracts
```

Create `app/Contracts/PaymentGatewayInterface.php`:

```php
<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Charge a payment
     * 
     * @param float $amount Amount in dollars (e.g., 99.99)
     * @param string $currency Currency code (USD)
     * @param array $paymentDetails Payment details (card number, CVV, etc.)
     * @param array $metadata Additional metadata (order ID, customer info, etc.)
     * @return array [
     *   'success' => bool,
     *   'transaction_id' => string,
     *   'message' => string,
     *   'raw_response' => array
     * ]
     */
    public function charge(float $amount, string $currency, array $paymentDetails, array $metadata = []): array;

    /**
     * Refund a payment
     * 
     * @param string $transactionId Original transaction ID
     * @param float|null $amount Amount to refund (null = full refund)
     * @return array ['success' => bool, 'message' => string]
     */
    public function refund(string $transactionId, ?float $amount = null): array;

    /**
     * Verify payment details without charging
     * 
     * @param array $paymentDetails Payment details to verify
     * @return array ['valid' => bool, 'message' => string]
     */
    public function verify(array $paymentDetails): array;

    /**
     * Get gateway name
     */
    public function getGatewayName(): string;

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array;

    /**
     * Check if gateway is in test mode
     */
    public function isTestMode(): bool;
}
```

### 2. Create Mock Payment Gateway Implementation

Create `app/Services/Payment/MockPaymentGateway.php`:

```php
<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Charge a payment (mock implementation)
     */
    public function charge(float $amount, string $currency, array $paymentDetails, array $metadata = []): array
    {
        Log::info('Mock payment gateway - Charge attempt', [
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $metadata,
        ]);

        // Simulate success/failure based on card number
        $cardNumber = $paymentDetails['card_number'] ?? '';
        $shouldSucceed = !str_ends_with($cardNumber, '0000'); // Cards ending in 0000 fail

        if ($shouldSucceed) {
            $transactionId = 'MOCK_' . Str::upper(Str::random(16));
            
            Log::info('Mock payment gateway - Success', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Payment successful (MOCK)',
                'raw_response' => [
                    'gateway' => 'mock',
                    'test_mode' => true,
                    'timestamp' => now()->toIso8601String(),
                ],
            ];
        } else {
            Log::warning('Mock payment gateway - Failure', [
                'amount' => $amount,
                'reason' => 'Test card ending in 0000',
            ]);

            return [
                'success' => false,
                'transaction_id' => null,
                'message' => 'Payment declined (MOCK - card ending in 0000)',
                'raw_response' => [
                    'gateway' => 'mock',
                    'error_code' => 'card_declined',
                    'test_mode' => true,
                ],
            ];
        }
    }

    /**
     * Refund a payment (mock implementation)
     */
    public function refund(string $transactionId, ?float $amount = null): array
    {
        Log::info('Mock payment gateway - Refund attempt', [
            'transaction_id' => $transactionId,
            'amount' => $amount ?? 'full refund',
        ]);

        // Mock gateway always succeeds refunds
        return [
            'success' => true,
            'refund_id' => 'REFUND_' . Str::upper(Str::random(16)),
            'message' => 'Refund successful (MOCK)',
        ];
    }

    /**
     * Verify payment details (mock implementation)
     */
    public function verify(array $paymentDetails): array
    {
        $cardNumber = $paymentDetails['card_number'] ?? '';
        $cvv = $paymentDetails['cvv'] ?? '';
        $expiry = $paymentDetails['expiry'] ?? '';

        // Basic validation
        $valid = strlen($cardNumber) >= 13 
            && strlen($cvv) >= 3 
            && !empty($expiry);

        return [
            'valid' => $valid,
            'message' => $valid ? 'Card details valid (MOCK)' : 'Invalid card details (MOCK)',
        ];
    }

    /**
     * Get gateway name
     */
    public function getGatewayName(): string
    {
        return 'Mock Payment Gateway';
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return ['USD', 'EUR', 'GBP'];
    }

    /**
     * Check if gateway is in test mode
     */
    public function isTestMode(): bool
    {
        return true;
    }
}
```

### 3. Create Payment Transaction Model & Migration

```bash
sail artisan make:model PaymentTransaction -m
```

Edit the migration file `database/migrations/YYYY_MM_DD_HHMMSS_create_payment_transactions_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Transaction details
            $table->string('transaction_id')->unique();
            $table->string('gateway')->default('mock'); // mock, authorize_net, square, etc.
            $table->enum('type', ['charge', 'refund', 'void'])->default('charge');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            
            // Amount
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Payment method (last 4 digits only for security)
            $table->string('payment_method')->nullable(); // e.g., "Visa ****1234"
            
            // Response data
            $table->text('gateway_response')->nullable(); // JSON encoded response
            $table->text('error_message')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['order_id', 'type']);
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
```

### 4. Update Payment Transaction Model

Edit `app/Models/PaymentTransaction.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'transaction_id',
        'gateway',
        'type',
        'status',
        'amount',
        'currency',
        'payment_method',
        'gateway_response',
        'error_message',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the transaction
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if transaction was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark transaction as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'processed_at' => now(),
        ]);
    }
}
```

### 5. Run Migration

```bash
sail artisan migrate
```

### 6. Create Payment Service

Create `app/Services/PaymentService.php`:

```php
<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private PaymentGatewayInterface $gateway
    ) {}

    /**
     * Process a payment
     */
    public function processPayment(
        User $user,
        float $amount,
        array $paymentDetails,
        ?int $orderId = null,
        array $metadata = []
    ): PaymentTransaction {
        return DB::transaction(function () use ($user, $amount, $paymentDetails, $orderId, $metadata) {
            // Create pending transaction record
            $transaction = PaymentTransaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'transaction_id' => 'PENDING_' . uniqid(),
                'gateway' => $this->gateway->getGatewayName(),
                'type' => 'charge',
                'status' => 'pending',
                'amount' => $amount,
                'currency' => 'USD',
                'metadata' => $metadata,
            ]);

            // Attempt payment through gateway
            $result = $this->gateway->charge($amount, 'USD', $paymentDetails, [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'transaction_id' => $transaction->id,
            ]);

            // Update transaction with result
            $transaction->update([
                'transaction_id' => $result['transaction_id'] ?? $transaction->transaction_id,
                'status' => $result['success'] ? 'completed' : 'failed',
                'gateway_response' => $result['raw_response'] ?? [],
                'error_message' => $result['success'] ? null : $result['message'],
                'payment_method' => $this->formatPaymentMethod($paymentDetails),
                'processed_at' => now(),
            ]);

            // Log transaction
            Log::info('Payment processed', [
                'transaction_id' => $transaction->id,
                'success' => $result['success'],
                'amount' => $amount,
                'user_id' => $user->id,
            ]);

            return $transaction;
        });
    }

    /**
     * Process a refund
     */
    public function processRefund(PaymentTransaction $transaction, ?float $amount = null): array
    {
        if ($transaction->status !== 'completed') {
            return [
                'success' => false,
                'message' => 'Can only refund completed transactions',
            ];
        }

        $result = $this->gateway->refund($transaction->transaction_id, $amount);

        if ($result['success']) {
            // Create refund transaction record
            PaymentTransaction::create([
                'user_id' => $transaction->user_id,
                'order_id' => $transaction->order_id,
                'transaction_id' => $result['refund_id'],
                'gateway' => $this->gateway->getGatewayName(),
                'type' => 'refund',
                'status' => 'completed',
                'amount' => $amount ?? $transaction->amount,
                'currency' => $transaction->currency,
                'metadata' => ['original_transaction_id' => $transaction->id],
                'processed_at' => now(),
            ]);

            $transaction->update(['status' => 'refunded']);
        }

        return $result;
    }

    /**
     * Format payment method for display (last 4 digits only)
     */
    private function formatPaymentMethod(array $paymentDetails): string
    {
        $cardNumber = $paymentDetails['card_number'] ?? 'Unknown';
        $last4 = substr($cardNumber, -4);
        $type = $this->detectCardType($cardNumber);
        
        return "{$type} ****{$last4}";
    }

    /**
     * Detect card type from number
     */
    private function detectCardType(string $cardNumber): string
    {
        $firstDigit = substr($cardNumber, 0, 1);
        $firstTwo = substr($cardNumber, 0, 2);

        return match(true) {
            $firstDigit === '4' => 'Visa',
            in_array($firstTwo, ['51', '52', '53', '54', '55']) => 'Mastercard',
            in_array($firstTwo, ['34', '37']) => 'Amex',
            $firstTwo === '60' => 'Discover',
            default => 'Card',
        };
    }
}
```

### 7. Register Payment Gateway in Service Provider

Edit `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\EmailServiceInterface;
use App\Contracts\PaymentGatewayInterface;
use App\Services\EmailService;
use App\Services\Payment\MockPaymentGateway;
use App\Services\StorageService;
use App\Services\PaymentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Email service binding
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);
        
        // Storage service binding
        $this->app->singleton(StorageService::class);
        
        // Payment gateway binding (swap this in production)
        $this->app->singleton(PaymentGatewayInterface::class, function ($app) {
            // In production, read from config to determine which gateway
            $gateway = config('services.payment.gateway', 'mock');
            
            return match($gateway) {
                'mock' => new MockPaymentGateway(),
                // 'authorize_net' => new AuthorizeNetGateway(), // Phase 7
                // 'square' => new SquareGateway(), // Phase 7
                default => new MockPaymentGateway(),
            };
        });
        
        // Payment service binding
        $this->app->singleton(PaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

### 8. Add Payment Configuration

Edit `config/services.php` and add:

```php
'payment' => [
    'gateway' => env('PAYMENT_GATEWAY', 'mock'),
    'test_mode' => env('PAYMENT_TEST_MODE', true),
],
```

### 9. Update Environment Variables

Edit `.env`:

```env
PAYMENT_GATEWAY=mock
PAYMENT_TEST_MODE=true
```

### 10. Create Test Route

Edit `routes/web.php` and add:

```php
use App\Services\PaymentService;

// Payment test route (REMOVE IN PRODUCTION)
Route::get('/test-payment', function (PaymentService $paymentService) {
    $user = auth()->user() ?? \App\Models\User::first();
    
    if (!$user) {
        return 'No users found. Create a user first.';
    }
    
    // Test successful payment
    $successTransaction = $paymentService->processPayment(
        user: $user,
        amount: 99.99,
        paymentDetails: [
            'card_number' => '4111111111111111', // Test Visa
            'cvv' => '123',
            'expiry' => '12/25',
            'name' => $user->name,
        ],
        metadata: ['test' => true, 'description' => 'Test payment']
    );
    
    // Test failed payment
    $failedTransaction = $paymentService->processPayment(
        user: $user,
        amount: 49.99,
        paymentDetails: [
            'card_number' => '4111111111110000', // Ends in 0000 = fails
            'cvv' => '123',
            'expiry' => '12/25',
            'name' => $user->name,
        ],
        metadata: ['test' => true, 'description' => 'Test failed payment']
    );
    
    return response()->json([
        'success_transaction' => [
            'id' => $successTransaction->id,
            'status' => $successTransaction->status,
            'amount' => $successTransaction->amount,
            'transaction_id' => $successTransaction->transaction_id,
        ],
        'failed_transaction' => [
            'id' => $failedTransaction->id,
            'status' => $failedTransaction->status,
            'amount' => $failedTransaction->amount,
            'error' => $failedTransaction->error_message,
        ],
    ], 200, [], JSON_PRETTY_PRINT);
})->name('test-payment');
```

### 11. Test Payment Processing

Visit: http://localhost/test-payment

You should see JSON output showing:
- Successful transaction with `transaction_id` starting with `MOCK_`
- Failed transaction with error message

### 12. Verify Database Records

```bash
sail artisan tinker
```

In Tinker:
```php
\App\Models\PaymentTransaction::all();
exit
```

You should see 2 transactions created.

### 13. Document Payment Architecture

Create `docs/payment-architecture.md`:

```markdown
# Payment Architecture - Pacific Edge Labs

## Overview
Payment system is abstracted to allow easy switching between payment processors. This is CRITICAL for peptide businesses as processors frequently drop vendors.

## Current Setup (Development)
- **Gateway:** Mock Payment Gateway
- **Purpose:** Development and testing
- **Test Cards:**
  - Success: Any card NOT ending in 0000
  - Failure: Any card ending in 0000

## Architecture Pattern

### Interface: `App\Contracts\PaymentGatewayInterface`
Defines all payment methods the application needs.

### Implementation: `App\Services\Payment\MockPaymentGateway`
Mock implementation for development.

### Service: `App\Services\PaymentService`
Business logic for processing payments, refunds, and transaction logging.

### Service Binding
Registered in `AppServiceProvider` - can be swapped via config.

## Production Gateway Options (Phase 7)

### Option 1: Authorize.Net (Recommended)
**Why:** Most stable for peptide/supplement industry  
**Cost:** $25/month + 2.9% + $0.30 per transaction  
**Setup:**
```bash
composer require authorizenet/authorizenet
```
Create `App\Services\Payment\AuthorizeNetGateway.php`  
Update config: `PAYMENT_GATEWAY=authorize_net`

### Option 2: Square
**Why:** Easy setup, good backup option  
**Cost:** 2.9% + $0.30 per transaction  
**Setup:**
```bash
composer require square/square
```

### Option 3: PayPal
**Why:** Widely accepted, last resort  
**Cost:** 3.49% + $0.49 per transaction (high fees)  
**Note:** May require business review for peptides

### Option 4: Crypto (Future)
**Why:** Processor-independent  
**Cost:** Network fees only  
**Tokens:** USDC, USDT on Ethereum/Polygon

## Transaction Logging

All payments logged in `payment_transactions` table:
- Transaction ID (unique)
- Gateway name
- Amount and currency
- Status (pending, completed, failed, refunded)
- Gateway response (JSON)
- Metadata

## Security Best Practices

### PCI Compliance
1. **NEVER store full card numbers** - Only last 4 digits
2. **NEVER store CVV** - Not even encrypted
3. **NEVER store expiry dates** - After transaction
4. Use tokenization when possible
5. SSL/TLS required for all payment forms

### Production Checklist (Phase 7)
- [ ] SSL certificate installed
- [ ] PCI compliance questionnaire completed
- [ ] Gateway test mode disabled
- [ ] Fraud detection enabled
- [ ] Transaction monitoring alerts
- [ ] Refund policy documented
- [ ] Chargeback handling process

## Test Card Numbers (Mock Gateway)

### Success
- 4111111111111111 (Visa)
- 5500000000000004 (Mastercard)
- 340000000000009 (Amex)

### Failure
- 4111111111110000 (any card ending in 0000)

## Refund Process

1. Admin initiates refund in Filament
2. `PaymentService::processRefund()` called
3. Gateway processes refund
4. New refund transaction created
5. Original transaction marked as 'refunded'
6. Customer notified via email

## Fraud Prevention (Phase 7)

### Address Verification Service (AVS)
Compare billing address with card issuer records

### CVV Verification
Verify security code matches

### Velocity Checks
Limit transactions per card/IP/user

### Geographic Blocking
Block high-risk countries

## Future Enhancements

### Saved Payment Methods
Store tokenized cards for repeat customers (Phase 5)

### Subscription Billing
Recurring payments for subscription products (Phase 9+)

### Split Payments
Allow partial payment + crypto (Phase 9+)

### Buy Now Pay Later
Integrate Affirm/Klarna (Phase 9+)
```

### 14. Commit Changes

```bash
git add .
git commit -m "Create payment abstraction layer with mock gateway"
git push
```

## Validation Checklist

- [ ] Payment gateway interface created
- [ ] Mock gateway implementation working
- [ ] PaymentTransaction model and migration created
- [ ] Migration run successfully
- [ ] PaymentService created
- [ ] Gateway bound in AppServiceProvider
- [ ] http://localhost/test-payment creates transactions
- [ ] Success transaction shows 'completed' status
- [ ] Failed transaction shows 'failed' status
- [ ] Transactions visible in database
- [ ] Documentation created (payment-architecture.md)

## Common Issues & Solutions

### Issue: "Class 'Order' not found"
**Solution:**
This is expected - Order model will be created in Phase 4. The `order_id` field is nullable for now.

### Issue: PaymentTransaction not saving
**Solution:**
Check migration ran:
```bash
sail artisan migrate:status
```

### Issue: Gateway not bound correctly
**Solution:**
Clear config cache:
```bash
sail artisan config:clear
sail artisan cache:clear
```

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-007 as complete
- ➡️ Proceed to TASK-0-008 (Lightsail Deployment)

## Time Estimate
**45-60 minutes**

## Success Criteria
- Payment abstraction layer created
- Mock gateway processing test payments
- Transaction logging working
- Refund capability implemented
- Documentation complete
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-001  
**Blocks:** TASK-0-004 (Checkout will use this)  
**Priority:** High
