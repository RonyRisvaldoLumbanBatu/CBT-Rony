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
        Schema::table('results', function (Blueprint $table) {
            $table->json('essay_scores')->nullable()->after('answers_data');
            $table->boolean('is_essay_graded')->default(false)->after('essay_scores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['essay_scores', 'is_essay_graded']);
        });
    }
};
