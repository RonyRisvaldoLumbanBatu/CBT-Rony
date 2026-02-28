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
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type', 50)->default('pg')->after('question_text');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->json('answers_data')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('answers_data');
        });
    }
};
