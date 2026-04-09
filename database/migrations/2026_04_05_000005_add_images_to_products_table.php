<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('image_path')->nullable()->after('delivery');
            $table->string('list_thumbnail_path')->nullable()->after('image_path');
            $table->string('detail_thumbnail_path')->nullable()->after('list_thumbnail_path');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'image_path',
                'list_thumbnail_path',
                'detail_thumbnail_path',
            ]);
        });
    }
};
