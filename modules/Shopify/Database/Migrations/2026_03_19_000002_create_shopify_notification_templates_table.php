<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_notification_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('shopify_store_id');
            $table->string('event_type'); // order_confirmation, shipping_update, delivery_status, cod_verification
            $table->unsignedBigInteger('template_id')->nullable(); // links to WhatsApp template
            $table->json('template_params')->nullable(); // mapping of template placeholders to Shopify data
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shopify_store_id')->references('id')->on('shopify_stores')->onDelete('cascade');
            $table->index('organization_id');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopify_notification_templates');
    }
};
