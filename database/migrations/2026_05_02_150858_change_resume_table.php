<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->integer('candidate_id')->nullable()->change();
            $table->dropColumn('file_name');
            $table->renameColumn('mime_type', 'mimetype');
            $table->dropColumn('size');
            $table->dropColumn('storage_disk');
            $table->dropColumn('summary');
            $table->unsignedTinyInteger('grade')->nullable();
            $table->text('text_grade')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('grade');
            $table->dropColumn('text_grade');
            $table->text('summary')->nullable();
            $table->string('storage_disk')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->renameColumn('mimetype', 'mime_type');
            $table->string('file_name')->nullable();
            $table->integer('candidate_id')->nullable(false)->change();
        });
    }
};
