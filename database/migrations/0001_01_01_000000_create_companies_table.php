<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('console_language', 10)->default('en');
            $table->string('interface_color', 7)->default('#2563eb');
            $table->string('logo_path')->nullable();
            $table->string('contact_person');
            $table->string('preferred_language', 10)->default('en');
            $table->text('about')->nullable();
            $table->json('addresses')->nullable();
            $table->json('phones')->nullable();
            $table->json('emails')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('time_zone')->default('UTC');
            $table->boolean('text_opt_in')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
