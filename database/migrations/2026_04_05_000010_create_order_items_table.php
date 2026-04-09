<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->nullable()->constrained()->nullOnDelete();
            $table->string('product_title');
            $table->string('product_slug')->nullable();
            $table->string('price_label', 50);
            $table->unsignedBigInteger('price_numeric');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('total_numeric');
            $table->string('download_path')->nullable();
            $table->string('download_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
