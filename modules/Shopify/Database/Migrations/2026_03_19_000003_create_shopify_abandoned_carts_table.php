<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('shopify_store_id');
            $table->string('shopify_checkout_id');
            $table->string('shopify_cart_token')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('recovery_url')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->json('line_items')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->enum('status', ['abandoned', 'reminder_1_sent', 'reminder_2_sent', 'reminder_3_sent', 'recovered', 'expired'])->default('abandoned');
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shopify_store_id')->references('id')->on('shopify_stores')->onDelete('cascade');
            $table->index('organization_id');
            $table->index('shopify_checkout_id');
            $table->index('status');
            $table->index('abandoned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopify_abandoned_carts');
    }
};
