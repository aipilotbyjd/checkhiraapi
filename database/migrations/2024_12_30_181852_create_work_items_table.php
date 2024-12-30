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
        Schema::create('work_items', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->integer('diamond')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->unsignedBigInteger('work_id')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_items');
    }
};
