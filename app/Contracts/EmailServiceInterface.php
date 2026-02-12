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
     * Send order confirmation email.
     *
     * @param Order $order The order to send confirmation for
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendOrderConfirmation(Order $order): bool;

    /**
     * Send notification that a Certificate of Analysis is ready for download.
     *
     * @param User $user The user to notify
     * @param string $productName The name of the product the CoA belongs to
     * @param string $coaUrl The URL where the CoA PDF can be downloaded
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendCoaReadyNotification(User $user, string $productName, string $coaUrl): bool;

    /**
     * Send password reset email with token link.
     *
     * @param User $user The user requesting the password reset
     * @param string $token The password reset token
     * @return bool True if email was sent successfully, false otherwise
     */
    public function sendPasswordResetEmail(User $user, string $token): bool;

    /**
     * Send an arbitrary mailable to a user.
     *
     * @param Mailable $mailable The mailable instance to send
     * @param User $user The recipient user
     * @return bool True if email was sent successfully, false otherwise
     */
    public function send(Mailable $mailable, User $user): bool;
}
