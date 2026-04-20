<?php

declare(strict_types=1);

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
        Schema::table('operations', static function (Blueprint $table) {
            $table->dropForeign('operations_interview_id_foreign');
            $table->dropColumn('interview_id');
            $table->dropColumn('type');
            $table->dropForeign('operations_resume_id_foreign');
            $table->dropColumn('resume_id');
            $table->morphs('operational');
            $table->enum('type', OperationType::values());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
