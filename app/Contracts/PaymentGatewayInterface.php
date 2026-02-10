<?php

namespace App\Contracts;

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
