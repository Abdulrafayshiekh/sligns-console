<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('app', 20);
            $table->date('activity_date');
            $table->string('label')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('value', 8, 2)->nullable();
            $table->text('content')->nullable();
            $table->boolean('shared')->default(false);
            $table->timestamps();
            $table->index(['client_id', 'app', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_activities');
    }
};
