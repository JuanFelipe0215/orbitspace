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
        Schema::create('blog_settings', function (Blueprint $table) {
            $table->id();
            $table->string('blog_name');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('bg_color')->default('#ffffff');
            $table->string('text_color')->default('#111111');
            $table->string('accent_color')->default('#3b82f6');
            $table->string('font')->default('serif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_settings');
    }
};
