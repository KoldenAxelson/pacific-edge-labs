<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentTransaction;
use App\Models\User;
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
        array $metadata = [],
        ?int $orderId = null
    ): PaymentTransaction {
        // Create pending transaction
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'transaction_id' => 'PENDING_' . uniqid(),
            'gateway' => $this->gateway->getGatewayName(),
            'type' => 'charge',
            'status' => 'pending',
            'amount' => $amount,
            'currency' => $metadata['currency'] ?? 'USD',
            'payment_method' => $this->formatPaymentMethod($paymentDetails),
            'metadata' => $metadata,
        ]);

        try {
            // Attempt charge
            $result = $this->gateway->charge(
                amount: $amount,
                currency: $metadata['currency'] ?? 'USD',
                paymentDetails: $paymentDetails,
                metadata: array_merge($metadata, [
                    'user_id' => $user->id,
                    'transaction_db_id' => $transaction->id,
                ])
            );

            // Update transaction with result
            $transaction->update([
                'transaction_id' => $result['transaction_id'] ?? $transaction->transaction_id,
                'status' => $result['success'] ? 'completed' : 'failed',
                'gateway_response' => json_encode($result['raw_response'] ?? []),
                'error_message' => $result['success'] ? null : $result['message'],
                'processed_at' => now(),
            ]);

            if ($result['success']) {
                Log::info('Payment processed successfully', [
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $amount,
                    'user_id' => $user->id,
                ]);
            } else {
                Log::warning('Payment failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $result['message'],
                    'user_id' => $user->id,
                ]);
            }

            return $transaction;

        } catch (\Exception $e) {
            Log::error('Payment processing exception', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            $transaction->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            return $transaction;
        }
    }

    /**
     * Process a refund
     */
    public function processRefund(
        PaymentTransaction $originalTransaction,
        ?float $amount = null
    ): PaymentTransaction {
        if ($originalTransaction->status !== 'completed') {
            throw new \Exception('Can only refund completed transactions');
        }

        // Create refund transaction
        $refundTransaction = PaymentTransaction::create([
            'user_id' => $originalTransaction->user_id,
            'order_id' => $originalTransaction->order_id,
            'transaction_id' => 'REFUND_PENDING_' . uniqid(),
            'gateway' => $this->gateway->getGatewayName(),
            'type' => 'refund',
            'status' => 'pending',
            'amount' => $amount ?? $originalTransaction->amount,
            'currency' => $originalTransaction->currency,
            'metadata' => [
                'original_transaction_id' => $originalTransaction->transaction_id,
                'refund_amount' => $amount ?? 'full',
            ],
        ]);

        try {
            // Attempt refund
            $result = $this->gateway->refund(
                transactionId: $originalTransaction->transaction_id,
                amount: $amount
            );

            // Update refund transaction
            $refundTransaction->update([
                'transaction_id' => $result['refund_id'] ?? $refundTransaction->transaction_id,
                'status' => $result['success'] ? 'completed' : 'failed',
                'gateway_response' => json_encode($result),
                'error_message' => $result['success'] ? null : $result['message'],
                'processed_at' => now(),
            ]);

            // Mark original as refunded if full refund
            if ($result['success'] && !$amount) {
                $originalTransaction->update(['status' => 'refunded']);
            }

            Log::info('Refund processed', [
                'refund_id' => $refundTransaction->transaction_id,
                'original_id' => $originalTransaction->transaction_id,
                'amount' => $amount ?? 'full',
            ]);

            return $refundTransaction;

        } catch (\Exception $e) {
            Log::error('Refund processing exception', [
                'error' => $e->getMessage(),
                'original_transaction_id' => $originalTransaction->transaction_id,
            ]);

            $refundTransaction->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            return $refundTransaction;
        }
    }

    /**
     * Verify payment details
     */
    public function verifyPaymentDetails(array $paymentDetails): array
    {
        return $this->gateway->verify($paymentDetails);
    }

    /**
     * Format payment method for storage (last 4 digits only)
     */
    private function formatPaymentMethod(array $paymentDetails): ?string
    {
        $cardNumber = $paymentDetails['card_number'] ?? '';
        
        if (empty($cardNumber)) {
            return null;
        }

        $last4 = substr($cardNumber, -4);
        $cardType = $this->detectCardType($cardNumber);

        return "{$cardType} ****{$last4}";
    }

    /**
     * Detect card type from number
     */
    private function detectCardType(string $cardNumber): string
    {
        $patterns = [
            'Visa' => '/^4/',
            'Mastercard' => '/^5[1-5]/',
            'Amex' => '/^3[47]/',
            'Discover' => '/^6(?:011|5)/',
        ];

        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $cardNumber)) {
                return $type;
            }
        }

        return 'Card';
    }

    /**
     * Get gateway info
     */
    public function getGatewayInfo(): array
    {
        return [
            'name' => $this->gateway->getGatewayName(),
            'test_mode' => $this->gateway->isTestMode(),
            'supported_currencies' => $this->gateway->getSupportedCurrencies(),
        ];
    }
}
