<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Order;
use Illuminate\Mail\Mailable;

/**
 * Email service interface defining the contract for email operations in the application.
 *
 * Establishes the public API for sending transactional emails including welcome emails,
 * order confirmations, password resets, and certificate of analysis notifications.
 * Implementations should handle mail delivery, error handling, and optional logging.
 */
interface EmailServiceInterface
{
    /**
     * Send welcome email to new user.
     *
     * @param User $user The user to send the welcome email to
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendWelcomeEmail(User $user): bool;

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation(Order $order): bool;

    /**
     * Send CoA ready notification
     */
    public function sendCoaReadyNotification(User $user, string $productName, string $coaUrl): bool;

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(User $user, string $token): bool;

    /**
     * Send generic mailable
     */
    public function send(Mailable $mailable, User $user): bool;
}
