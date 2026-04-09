<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;

class OrderPaymentService
{
    public function applyMidtransPayload(Order $order, array $payload): Order
    {
        $transactionStatus = (string) data_get($payload, 'transaction_status', 'pending');
        $fraudStatus = data_get($payload, 'fraud_status');
        $paymentStatus = $this->resolvePaymentStatus($transactionStatus, $fraudStatus);

        if ($order->payment_status === 'paid' && $paymentStatus !== 'paid') {
            $paymentStatus = 'paid';
        }

        $paidAt = $order->paid_at;
        if ($paymentStatus === 'paid' && ! $paidAt) {
            $paidAt = $this->parseTimestamp(data_get($payload, 'settlement_time'))
                ?? $this->parseTimestamp(data_get($payload, 'transaction_time'))
                ?? now();
        }

        $order->forceFill([
            'payment_status' => $paymentStatus,
            'midtrans_transaction_status' => $transactionStatus,
            'midtrans_transaction_id' => data_get($payload, 'transaction_id'),
            'midtrans_payment_type' => data_get($payload, 'payment_type'),
            'midtrans_fraud_status' => $fraudStatus,
            'payment_payload' => $payload,
            'paid_at' => $paidAt,
            'expires_at' => $this->parseTimestamp(data_get($payload, 'expiry_time')) ?? $order->expires_at,
        ])->save();

        return $order->refresh();
    }

    private function resolvePaymentStatus(string $transactionStatus, mixed $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? 'pending' : 'paid',
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny', 'failure' => 'failed',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            default => 'pending',
        };
    }

    private function parseTimestamp(mixed $value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }
}
