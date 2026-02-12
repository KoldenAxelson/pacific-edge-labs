<?php

namespace App\Services;

use App\Contracts\EmailServiceInterface;
use App\Models\User;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Email service for handling all transactional emails in the application.
 *
 * Provides methods to send user welcome emails, order confirmations, payment notifications,
 * and certificate of analysis (CoA) ready notifications. Acts as the concrete implementation
 * of EmailServiceInterface, abstracting away mail delivery details from the rest of the
 * application. Currently logs email operations for phase development.
 *
 * @see \App\Contracts\EmailServiceInterface
 */
class EmailService implements EmailServiceInterface
{
    /**
     * Send welcome email to new user.
     *
     * @param User $user The user to send the welcome email to
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            // Will implement actual mailable in Phase 5
            Log::info("Welcome email would be sent to: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send order confirmation email.
     *
     * @param Order $order The order to send confirmation for
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            // Will implement in Phase 4
            Log::info("Order confirmation would be sent to: {$order->user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send CoA ready notification.
     *
     * @param User $user The user to notify
     * @param string $productName The name of the product
     * @param string $coaUrl The URL to the certificate of analysis
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendCoaReadyNotification(User $user, string $productName, string $coaUrl): bool
    {
        try {
            // Will implement in Phase 3
            Log::info("CoA ready notification would be sent to: {$user->email}");
            Log::info("Product: {$productName}, URL: {$coaUrl}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send CoA notification: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send password reset email.
     *
     * @param User $user The user to send reset email to
     * @param string $token The password reset token
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendPasswordResetEmail(User $user, string $token): bool
    {
        try {
            // Laravel handles this via Notifications
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send password reset: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send generic mailable.
     *
     * @param Mailable $mailable The mailable instance to send
     * @param User $user The user to send the email to
     * @return bool True if email was sent successfully, false otherwise
     */
    public function send(Mailable $mailable, User $user): bool
    {
        try {
            Mail::to($user->email)->send($mailable);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email: {$e->getMessage()}");
            return false;
        }
    }
}
