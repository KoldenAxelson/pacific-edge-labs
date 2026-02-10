<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Order;
use Illuminate\Mail\Mailable;

interface EmailServiceInterface
{
    /**
     * Send welcome email to new user
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
