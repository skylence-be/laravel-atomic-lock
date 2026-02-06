<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locks', function (Blueprint $table) {
            $table->id();
            $table->morphs('lockable');
            $table->string('owner')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('acquired_at');
            $table->timestamp('released_at')->nullable();
            $table->timestamp('expires_at')->index();

            $table->index(['lockable_type', 'lockable_id', 'released_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locks');
    }
};
