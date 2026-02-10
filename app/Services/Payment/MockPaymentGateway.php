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
