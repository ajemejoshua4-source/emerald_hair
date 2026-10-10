<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('order_number')->unique();
            $t->string('customer_name');
            $t->string('email')->nullable();
            $t->string('phone');
            $t->text('address')->nullable();
            $t->text('notes')->nullable();
            $t->decimal('total', 12, 2)->default(0);
            $t->string('status')->default('pending');
            $t->string('payment_status')->default('unpaid');
            $t->timestamps();
        });
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('product_name');
            $t->decimal('unit_price', 12, 2)->nullable();
            $t->integer('quantity');
            $t->decimal('subtotal', 12, 2)->nullable();
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
