<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        Schema::create('runs', function (Blueprint $table) {
            $table->id();
            $table->string('count');
            $table->string('level');

            $table->text('dungeons');

            $table->text('boosters');
            $table->string('boosters_count');

            $table->string('price');
            $table->string('unit');

            $table->string('adv')->nullable();
            $table->text('note')->nullable();

            $table->tinyInteger('paid')->default(0);
            $table->tinyInteger('depleted')->default(0);

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('channel_id')->nullable()->constrained('channels');
            $table->string('dmessage_id')->nullable();

            $table->text('dmessage_link')->nullable();

            $table->dateTime('paid_at')->nullable();
            $table->text('message')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('runs');
    }
};
