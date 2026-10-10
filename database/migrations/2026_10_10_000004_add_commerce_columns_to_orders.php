<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->string('currency', 3)->default('NGN');
            $table->unsignedBigInteger('delivery_zone_id')->nullable();
            $table->string('payment_reference')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('variant_label')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variant_id', 'variant_label']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'delivery_fee',
                'currency',
                'delivery_zone_id',
                'payment_reference',
                'paid_at',
            ]);
        });
    }
};
