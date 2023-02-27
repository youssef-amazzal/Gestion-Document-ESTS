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
        Schema::create('file_tag', function (Blueprint $table) {
            $table->id();

            $table->foreignId   ('file_id')
                  ->constrained ('files')
                  ->onDelete    ('cascade');

            $table->foreignId   ('tag_id')
                  ->constrained ('tags')
                  ->onDelete    ('cascade');

            $table->unique(['file_id', 'tag_id']);
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
        Schema::dropIfExists('file_tag');
    }
};
