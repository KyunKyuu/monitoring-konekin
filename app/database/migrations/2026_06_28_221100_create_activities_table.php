<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->string('title');
            $table->string('theme')->nullable();
            $table->dateTime('scheduled_at');
            $table->string('location')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('summary_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
