<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Interview\Enum\{Status as InterviewStatus};

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('status', InterviewStatus::values())
                ->default(InterviewStatus::Pending);
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            //
        });
    }
};
