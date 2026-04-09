<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransSnapService
{
    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
    }

    public function clientKey(): ?string
    {
        $clientKey = config('services.midtrans.client_key');

        return filled($clientKey) ? (string) $clientKey : null;
    }

    public function snapScriptUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function createSnapTransaction(Order $order): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Midtrans belum dikonfigurasi.');
        }

        $payload = $this->buildTransactionPayload($order->loadMissing('items'));

        try {
            $response = Http::withBasicAuth($this->serverKey(), '')
                ->acceptJson()
                ->post($this->snapBaseUrl().'/snap/v1/transactions', $payload)
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            throw $this->mapRequestException($exception);
        }

        return [
            'token' => (string) data_get($response, 'token'),
            'redirect_url' => data_get($response, 'redirect_url'),
            'payload' => $response,
        ];
    }

    public function fetchTransactionStatus(Order $order): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Midtrans belum dikonfigurasi.');
        }

        try {
            return Http::withBasicAuth($this->serverKey(), '')
                ->acceptJson()
                ->get($this->apiBaseUrl()."/v2/{$order->midtrans_order_id}/status")
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            throw $this->mapRequestException($exception);
        }
    }

    public function verifyNotificationSignature(array $payload): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $orderId = (string) data_get($payload, 'order_id');
        $statusCode = (string) data_get($payload, 'status_code');
        $grossAmount = (string) data_get($payload, 'gross_amount');
        $signature = (string) data_get($payload, 'signature_key');

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey());

        return hash_equals($expectedSignature, $signature);
    }

    private function buildTransactionPayload(Order $order): array
    {
        return [
            'transaction_details' => [
                'order_id' => $order->midtrans_order_id,
                'gross_amount' => $order->total_amount,
            ],
            'item_details' => $order->items->map(function (OrderItem $item): array {
                return [
                    'id' => (string) ($item->product_id ?: $item->id),
                    'price' => (int) $item->price_numeric,
                    'quantity' => (int) $item->quantity,
                    'name' => Str::limit($item->product_title, 50, ''),
                ];
            })->values()->all(),
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
            ],
            'callbacks' => [
                'finish' => route('orders.index', absolute: true),
            ],
        ];
    }

    private function mapRequestException(RequestException $exception): RuntimeException
    {
        $status = $exception->response?->status();

        if ($status === 401) {
            $environment = $this->isProduction() ? 'production' : 'sandbox';

            return new RuntimeException(
                "Midtrans menolak kredensial API. Periksa MIDTRANS_SERVER_KEY dan pastikan key tersebut cocok dengan environment {$environment}. MIDTRANS_CLIENT_KEY tidak menyebabkan error 401 ini."
            );
        }

        if ($status === 403) {
            return new RuntimeException('Midtrans menolak request ini. Periksa izin akun Midtrans atau pembatasan pada kredensial API.');
        }

        return new RuntimeException(
            'Midtrans gagal merespons request pembayaran dengan benar. Status HTTP: '.($status ?? 'unknown'),
            previous: $exception,
        );
    }

    private function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    private function serverKey(): string
    {
        return (string) config('services.midtrans.server_key');
    }

    private function snapBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    private function apiBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
