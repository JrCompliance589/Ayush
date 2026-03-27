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
        Schema::table('country_pricing', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Drop old unique on country_code alone, add composite unique
            $table->dropUnique(['country_code']);
            $table->unique(['user_id', 'country_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_pricing', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'country_code']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unique('country_code');
        });
    }
};
