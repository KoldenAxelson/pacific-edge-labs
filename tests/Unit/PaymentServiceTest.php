<?php

namespace Tests\Unit;

use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for the PaymentService.
 *
 * Verifies payment processing, refund handling, transaction creation,
 * and payment method masking with the mock payment gateway.
 */
class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = app(PaymentService::class);
        $this->user = User::factory()->create();
    }

    public function test_successful_payment_creates_transaction(): void
    {
        $transaction = $this->paymentService->processPayment(
            user: $this->user,
            amount: 99.99,
            paymentDetails: [
                'card_number' => '4111111111111111',
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $this->assertInstanceOf(PaymentTransaction::class, $transaction);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals(99.99, $transaction->amount);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    public function test_failed_payment_creates_failed_transaction(): void
    {
        $transaction = $this->paymentService->processPayment(
            user: $this->user,
            amount: 49.99,
            paymentDetails: [
                'card_number' => '4111111111110000', // Ends in 0000 = mock failure trigger
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $this->assertEquals('failed', $transaction->status);
        $this->assertNotNull($transaction->error_message);
    }

    public function test_payment_method_is_masked(): void
    {
        $transaction = $this->paymentService->processPayment(
            user: $this->user,
            amount: 99.99,
            paymentDetails: [
                'card_number' => '4111111111111111',
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $this->assertStringContainsString('****1111', $transaction->payment_method);
        $this->assertStringContainsString('Visa', $transaction->payment_method);
    }

    public function test_refund_creates_refund_transaction(): void
    {
        $payment = $this->paymentService->processPayment(
            user: $this->user,
            amount: 99.99,
            paymentDetails: [
                'card_number' => '4111111111111111',
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $refundTransaction = $this->paymentService->processRefund($payment);

        $this->assertEquals('completed', $refundTransaction->status);
        $this->assertEquals('refunded', $payment->fresh()->status);
        $this->assertDatabaseHas('payment_transactions', [
            'type' => 'refund',
            'user_id' => $this->user->id,
        ]);
    }
}
