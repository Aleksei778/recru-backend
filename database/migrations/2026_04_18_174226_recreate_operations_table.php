<?php

declare(strict_types=1);

use App\Ai\Operation\Enum\{Type, Status};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('operations');

        Schema::create('operations', static function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->enum('type', Type::values());
            $table->enum('status', Status::values())->default(Status::Pending);
            $table->json('raw_response')->nullable();
            $table->json('raw_request')->nullable();
            $table->text('result')->nullable();
            $table->string('provider');
            $table->string('provider_id')->nullable();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
