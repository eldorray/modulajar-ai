<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rpps', function (Blueprint $table): void {
            $table->json('generation_input')->nullable()->after('content_result');
            $table->string('idempotency_key', 128)->nullable()->after('generation_input');
            $table->string('request_fingerprint', 64)->nullable()->after('idempotency_key');
            $table->string('failure_code', 64)->nullable()->after('status');
            $table->string('failure_message')->nullable()->after('failure_code');
            $table->timestamp('started_at')->nullable()->after('failure_message');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('failed_at')->nullable()->after('completed_at');
            $table->unique(['user_id', 'idempotency_key'], 'rpps_user_idempotency_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table): void {
            $table->dropUnique('rpps_user_idempotency_unique');
            $table->dropColumn([
                'generation_input', 'idempotency_key', 'request_fingerprint',
                'failure_code', 'failure_message', 'started_at', 'completed_at', 'failed_at',
            ]);
        });
    }
};
