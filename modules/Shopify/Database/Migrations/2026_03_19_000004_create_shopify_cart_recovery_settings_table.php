<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_cart_recovery_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('shopify_store_id');
            $table->boolean('is_active')->default(false);

            // Reminder 1 - 30 min
            $table->boolean('reminder_1_enabled')->default(true);
            $table->unsignedInteger('reminder_1_delay_minutes')->default(30);
            $table->unsignedBigInteger('reminder_1_template_id')->nullable();
            $table->json('reminder_1_params')->nullable();

            // Reminder 2 - 6 hours
            $table->boolean('reminder_2_enabled')->default(true);
            $table->unsignedInteger('reminder_2_delay_minutes')->default(360);
            $table->unsignedBigInteger('reminder_2_template_id')->nullable();
            $table->json('reminder_2_params')->nullable();

            // Reminder 3 - 24 hours with discount
            $table->boolean('reminder_3_enabled')->default(true);
            $table->unsignedInteger('reminder_3_delay_minutes')->default(1440);
            $table->unsignedBigInteger('reminder_3_template_id')->nullable();
            $table->json('reminder_3_params')->nullable();
            $table->string('discount_code')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();

            $table->timestamps();

            $table->foreign('shopify_store_id')->references('id')->on('shopify_stores')->onDelete('cascade');
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopify_cart_recovery_settings');
    }
};
