<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique();
            $table->string('phone_code')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('phone_verification_token', 64)->nullable();
            $table->timestamp('phone_verification_token_expires_at')->nullable();
            $table->timestamp('phone_verification_token_sent_at')->nullable();
            $table->integer('phone_verification_token_sent_count')->default(0);
            $table->string('phone_verification_token_sent_ip', 45)->nullable();
            $table->text('phone_verification_token_sent_user_agent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('phone_code');
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('phone_verification_token');
            $table->dropColumn('phone_verification_token_expires_at');
            $table->dropColumn('phone_verification_token_sent_at');
            $table->dropColumn('phone_verification_token_sent_count');
            $table->dropColumn('phone_verification_token_sent_ip');
            $table->dropColumn('phone_verification_token_sent_user_agent');
        });
    }
};
