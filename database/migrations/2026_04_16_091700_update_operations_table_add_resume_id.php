<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->foreignId('interview_id')->nullable()->change();
            $table->foreignId('resume_id')->nullable()->constrained('resumes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['resume_id']);
            $table->dropColumn('resume_id');
            $table->foreignId('interview_id')->nullable(false)->change();
        });
    }
};
