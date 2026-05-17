<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('subject');
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('body');
            $table->string('status', 255)->nullable()->after('body');
        });
    }
};
