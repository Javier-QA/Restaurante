<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_queries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('question');

            $table->longText('sql_query')->nullable();

            $table->unsignedInteger('result_count')
                ->default(0);

            $table->string('chart_type', 30)
                ->nullable();

            $table->boolean('is_favorite')
                ->default(false);

            $table->timestamps();

            $table->index([
                'user_id',
                'is_favorite',
                'created_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_queries');
    }
};