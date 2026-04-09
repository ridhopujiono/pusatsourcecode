<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('midtrans_order_id')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->unsignedBigInteger('subtotal_amount');
            $table->unsignedBigInteger('total_amount');
            $table->string('payment_status', 30)->default('pending');
            $table->string('midtrans_transaction_status', 50)->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type', 50)->nullable();
            $table->string('midtrans_fraud_status', 50)->nullable();
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->json('snap_payload')->nullable();
            $table->json('payment_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
