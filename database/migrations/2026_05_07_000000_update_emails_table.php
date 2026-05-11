<?php

declare(strict_types=1);

use App\Email\Enum\Status;
use App\Email\Enum\Type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->renameColumn('user_id', 'sender_id');
            $table->nullableMorphs('recipient');
            $table->enum('status', Status::values())->default(Status::Pending->value)->after('recipient_id');
            $table->enum('type', Type::values())->after('status');
            $table->string('subject')->after('type');
            $table->timestamp('sent_at')->nullable()->after('subject');
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->renameColumn('sender_id', 'user_id');
            $table->dropMorphs('recipient');
            $table->dropColumn(['status', 'type', 'subject', 'sent_at']);
        });
    }
};