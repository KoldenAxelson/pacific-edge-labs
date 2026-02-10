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
Defines all payment methods the application needs:
- `charge()` - Process a payment
- `refund()` - Refund a transaction
- `verify()` - Validate payment details
- `getGatewayName()` - Get gateway identifier
- `getSupportedCurrencies()` - List accepted currencies
- `isTestMode()` - Check if in test mode

### Implementation: `App\Services\Payment\MockPaymentGateway`
Mock implementation for development:
- Simulates successful payments for most cards
- Cards ending in 0000 trigger failures
- Generates mock transaction IDs
- Logs all payment attempts
- Always succeeds refunds

### Service: `App\Services\PaymentService`
Business logic for payment processing:
- `processPayment()` - Handle payment with transaction logging
- `processRefund()` - Handle refunds with validation
- `verifyPaymentDetails()` - Validate payment info
- `getGatewayInfo()` - Get current gateway details

### Model: `App\Models\PaymentTransaction`
Database model for transaction history:
- All payment attempts logged (success and failure)
- Links to User and Order models
- Stores last 4 card digits only (PCI compliant)
- Records gateway responses for debugging
- Tracks refunds separately

### Service Binding
Registered in `AppServiceProvider`:
```php
$this->app->singleton(PaymentGatewayInterface::class, function ($app) {
    return new MockPaymentGateway();
});
```

To swap gateways, just change this binding. No code changes needed elsewhere!

## Production Gateway Options (Phase 7)

### Option 1: Authorize.Net (Recommended)
**Why:** Most stable for peptide/supplement industry  
**Cost:** $25/month + 2.9% + $0.30 per transaction  
**Setup:**
```bash
composer require authorizenet/authorizenet
```
Create `App\Services\Payment\AuthorizeNetGateway.php`  
Update binding in `AppServiceProvider`

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
- **transaction_id** - Unique identifier from gateway
- **gateway** - Which processor handled it (mock, authorize_net, etc.)
- **type** - charge, refund, or void
- **status** - pending, completed, failed, refunded
- **amount** - Dollar amount
- **currency** - USD, EUR, GBP, etc.
- **payment_method** - "Visa ****1234" (last 4 only)
- **gateway_response** - Full gateway response (JSON)
- **error_message** - Failure reason if applicable
- **metadata** - Order ID, customer info, etc.
- **processed_at** - When transaction completed

## Security Best Practices

### PCI Compliance
1. **NEVER store full card numbers** - Only last 4 digits
2. **NEVER store CVV** - Not even encrypted
3. **NEVER store expiry dates** - After transaction
4. Use tokenization when possible
5. SSL/TLS required for all payment forms

### Payment Data Flow
```
User enters card → Sent directly to gateway → Gateway returns token
We store: transaction ID + last 4 digits ONLY
```

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

## Development Testing

### Test Successful Payment
Visit: `http://localhost/test-payment`

Expected output:
```json
{
  "gateway_info": {
    "name": "Mock Payment Gateway",
    "test_mode": true,
    "supported_currencies": ["USD", "EUR", "GBP"]
  },
  "success_transaction": {
    "status": "completed",
    "transaction_id": "MOCK_..."
  },
  "failed_transaction": {
    "status": "failed",
    "error": "Payment declined (MOCK - card ending in 0000)"
  }
}
```

### Test via Tinker
```php
sail artisan tinker

$user = App\Models\User::first();
$paymentService = app(App\Services\PaymentService::class);

// Test payment
$transaction = $paymentService->processPayment(
    user: $user,
    amount: 99.99,
    paymentDetails: [
        'card_number' => '4111111111111111',
        'cvv' => '123',
        'expiry' => '12/25',
        'name' => $user->name,
    ],
    metadata: ['description' => 'Test order']
);

// Check result
$transaction->isSuccessful(); // true
$transaction->transaction_id; // MOCK_...

// Test refund
$refund = $paymentService->processRefund($transaction);
$refund->isSuccessful(); // true

exit
```

## Future Enhancements

### Saved Payment Methods (Phase 5)
Store tokenized cards for repeat customers

### Subscription Billing (Phase 9+)
Recurring payments for subscription products

### Split Payments (Phase 9+)
Allow partial payment + crypto

### Buy Now Pay Later (Phase 9+)
Integrate Affirm/Klarna

## Error Handling

All payment errors are caught and logged. Transaction status will be 'failed' with error message stored.

Common errors:
- **Insufficient funds** - Customer's bank declined
- **Invalid card** - Card number or CVV incorrect
- **Expired card** - Card past expiration date
- **Gateway timeout** - Network issue, retry
- **Fraud detected** - Gateway blocked transaction

## Monitoring & Alerts (Phase 7)

Set up alerts for:
- Failed transaction rate > 10%
- Chargebacks received
- Unusual transaction patterns
- Gateway downtime

## Cost Analysis

For $50M annual revenue target:

**Authorize.Net:**
- Monthly: $25
- Per transaction: 2.9% + $0.30
- Estimated annual cost: ~$1.5M (3% of revenue)

**Square:**
- Monthly: $0
- Per transaction: 2.9% + $0.30
- Estimated annual cost: ~$1.5M (3% of revenue)

**PayPal:**
- Monthly: $0
- Per transaction: 3.49% + $0.49
- Estimated annual cost: ~$1.75M (3.5% of revenue)

## Gateway Switching Example

To switch from Mock to Authorize.Net:

1. Install package:
```bash
composer require authorizenet/authorizenet
```

2. Create implementation:
```php
// app/Services/Payment/AuthorizeNetGateway.php
class AuthorizeNetGateway implements PaymentGatewayInterface
{
    public function charge(...) {
        // Use Authorize.Net SDK
    }
    // ... implement other methods
}
```

3. Update binding:
```php
// app/Providers/AppServiceProvider.php
$this->app->singleton(PaymentGatewayInterface::class, function ($app) {
    return new AuthorizeNetGateway();
});
```

4. Add credentials to `.env`:
```env
PAYMENT_GATEWAY=authorize_net
AUTHORIZE_NET_LOGIN_ID=your_login_id
AUTHORIZE_NET_TRANSACTION_KEY=your_key
PAYMENT_TEST_MODE=false
```

**No other code changes needed!** The abstraction layer handles everything.
