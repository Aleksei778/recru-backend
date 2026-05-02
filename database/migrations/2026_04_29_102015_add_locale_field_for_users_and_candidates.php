<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Common\Enum\Locale;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('locale', Locale::values())->default(Locale::RU);
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->enum('locale', Locale::values())->default(Locale::RU);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
