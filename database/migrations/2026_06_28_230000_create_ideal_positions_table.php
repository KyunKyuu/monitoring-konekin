<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ideal_positions', function (Blueprint $table) {
            $table->id();
            $table->string('function_name');
            $table->string('position_name');
            $table->text('goal')->nullable();
            $table->text('responsibilities')->nullable();
            $table->unsignedInteger('required_count')->default(1);
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ideal_positions');
    }
};
