<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\VoiceLog\Enum\Type;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voice_logs', function (Blueprint $table) {
            $table->dropMorphs('voiceable');
            $table->morphs('subject');
            $table->dropColumn('duration');
            $table->dropColumn('size');
            $table->dropColumn('yandex_id');
            $table->dropColumn('raw_response');
            $table->dropColumn('try_count');
            $table->string('audio_path');
            $table->enum('type', Type::values());
        });
    }

    public function down(): void
    {

    }
};
