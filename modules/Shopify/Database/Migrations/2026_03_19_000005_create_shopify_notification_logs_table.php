<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('shopify_store_id');
            $table->string('event_type');
            $table->string('shopify_resource_id')->nullable(); // order id, checkout id, etc.
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('chat_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('shopify_store_id')->references('id')->on('shopify_stores')->onDelete('cascade');
            $table->index('organization_id');
            $table->index('event_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopify_notification_logs');
    }
};
