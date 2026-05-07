<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menggabungkan semua evolusi tabel chats:
// create_chats + add_media + add_is_delivered + add_extra_fields
// + make_message_nullable + add_usaha_id + add_product_id

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('usaha_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('message')->nullable();
            $table->string('type')->default('text');
            $table->string('attachment')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->foreignId('reply_to_id')->nullable()->constrained('chats')->onDelete('set null');
            $table->boolean('deleted_by_sender')->default(false);
            $table->boolean('deleted_by_receiver')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamps();

            $table->foreign('usaha_id')->references('id')->on('usaha')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('produk')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
