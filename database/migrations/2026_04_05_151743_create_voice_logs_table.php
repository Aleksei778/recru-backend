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
        Schema::create('voice_logs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('voiceable');
            $table->unsignedInteger('duration')->nullable()->comment('seconds');
            $table->unsignedBigInteger('size')->nullable()->comment('bytes');
            $table->string('mimetype', 127)->nullable();
            $table->string('yandex_id')->nullable()->index();
            $table->json('raw_response')->nullable();
            $table->unsignedTinyInteger('try_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_logs');
    }
};
