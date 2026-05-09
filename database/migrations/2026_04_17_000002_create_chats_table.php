<?php

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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('usaha_id')->nullable()->constrained('usaha')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('produk')->nullOnDelete();
            $table->foreignId('reply_to_id')->nullable()->constrained('chats')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('type')->default('text');
            $table->string('attachment')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->boolean('deleted_by_sender')->default(false);
            $table->boolean('deleted_by_receiver')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
