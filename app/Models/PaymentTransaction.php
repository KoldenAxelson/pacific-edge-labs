<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PaymentTransaction model representing payment operations in the system.
 *
 * Tracks all financial transactions including charges and refunds processed through
 * payment gateways. Records transaction status, gateway responses, and error details
 * for auditing and compliance purposes. Maintains relationships with users and orders,
 * enabling comprehensive payment history tracking and financial reconciliation.
 */
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
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     *
     * @return BelongsTo The user relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the transaction.
     *
     * @return BelongsTo The order relationship
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if transaction was successful.
     *
     * @return bool True if transaction status is completed
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction failed.
     *
     * @return bool True if transaction status is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is pending.
     *
     * @return bool True if transaction status is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction was refunded.
     *
     * @return bool True if transaction status is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Scope to get completed transactions only.
     *
     * @param mixed $query The query builder instance
     * @return mixed The filtered query
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed transactions only.
     *
     * @param mixed $query The query builder instance
     * @return mixed The filtered query
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get transactions for a specific gateway.
     *
     * @param mixed $query The query builder instance
     * @param string $gateway The gateway name to filter by
     * @return mixed The filtered query
     */
    public function scopeGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Scope to get charge transactions only.
     *
     * @param mixed $query The query builder instance
     * @return mixed The filtered query
     */
    public function scopeCharges($query)
    {
        return $query->where('type', 'charge');
    }

    /**
     * Scope to get refund transactions only.
     *
     * @param mixed $query The query builder instance
     * @return mixed The filtered query
     */
    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }
}
