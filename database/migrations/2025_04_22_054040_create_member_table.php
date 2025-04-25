<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('member_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('min_points')->default(0);
            $table->decimal('points_multiplier', 8, 2)->default(1.0);
            $table->text('benefits')->nullable();
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->integer('points')->default(0);
            $table->unsignedBigInteger('tier_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tier_id')->references('id')->on('member_tiers');
        });

        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->integer('points');
            $table->enum('type', ['earn', 'redeem', 'expire', 'adjustment']);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });

        // Add member_id to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable()->after('user_id');
            $table->decimal('points_earned', 10, 2)->nullable();
            $table->decimal('points_redeemed', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['member_id', 'points_earned', 'points_redeemed', 'discount_amount']);
        });
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('members');
        Schema::dropIfExists('member_tiers');
    }
};
