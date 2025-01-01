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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('source_id')->references('id')->on('payment_sources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
