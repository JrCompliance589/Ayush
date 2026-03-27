<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('country_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 10)->unique();
            $table->string('country_name', 100);
            $table->decimal('marketing_price', 12, 4)->default(0);
            $table->decimal('utility_price', 12, 4)->default(0);
            $table->decimal('auth_price', 12, 4)->default(0);
            $table->timestamps();
        });

        // Seed India with default prices
        \DB::table('country_pricing')->insert([
            'country_code' => '91',
            'country_name' => 'India',
            'marketing_price' => 0.85,
            'utility_price' => 0.15,
            'auth_price' => 0.15,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_pricing');
    }
};
