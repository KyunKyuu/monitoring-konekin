<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ideal_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('candidate');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['ideal_position_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_candidates');
    }
};
