<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            // fixed / percentage
            $table->enum('type', [
                'fixed',
                'percentage',
            ]);
            $table->decimal('value', 10, 2);

            // Minimum order
            $table->decimal('minimum_amount', 10, 2)
                ->default(0);

            // Maximum discount (percentage only)
            $table->decimal('maximum_discount', 10, 2)
                ->nullable();

            // Total usage
            $table->unsignedInteger('usage_limit')
                ->nullable();

            // Per customer
            $table->unsignedInteger('usage_per_user')
                ->default(1);

            // Already used
            $table->unsignedInteger('used_count')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamp('starts_at')
                ->nullable();
            $table->timestamp('expires_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
