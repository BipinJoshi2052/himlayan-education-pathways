<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('page_slug')->index();
            $table->string('key');
            $table->string('layout_type');
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('content')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['page_slug', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
