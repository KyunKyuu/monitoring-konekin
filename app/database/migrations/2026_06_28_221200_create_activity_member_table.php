<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('role_in_activity')->default('participant');
            $table->string('attendance_status')->default('planned');
            $table->timestamps();

            $table->unique(['activity_id', 'member_id', 'role_in_activity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_member');
    }
};
