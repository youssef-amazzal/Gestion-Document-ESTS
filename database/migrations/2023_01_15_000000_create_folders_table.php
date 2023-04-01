<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string              ('name', 100);
            $table->text                ('description')->nullable();
            $table->unsignedBigInteger  ('size')->default(0);
            $table->boolean             ('is_pinned')->default(false);

            $table->foreignId('owner_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->boolean('is_shortcut')->default(false);
            $table->foreignId('original_id')
                  ->nullable()
                  ->constrained('folders')
                  ->onDelete('cascade');

            $table->foreignId('parent_folder_id')
                  ->nullable()
                  ->constrained('folders')
                  ->onDelete('cascade');

            $table->foreignId('space_id')
                  ->constrained('spaces')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder');
    }
};
