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
        Schema::create('folder_tag', function (Blueprint $table) {
            $table->id();

            $table->foreignId   ('folder_id')
                  ->constrained ('folders')
                  ->onDelete    ('cascade');

            $table->foreignId   ('tag_id')
                    ->constrained ('tags')
                    ->onDelete    ('cascade');

            $table->unique(['folder_id', 'tag_id']);

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
        Schema::dropIfExists('folder_tag');
    }
};
