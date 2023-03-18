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
        /*
         * todo : find a way to implement short links to files and folders
         *  currently, this table is not used. Later, it may or may not be used if we wanted to implement
         *  a way for a user to have short links to files and folders that he does or doesn't own.
         */
        Schema::create('file_folder', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id')
                  ->constrained('files')
                  ->onDelete('cascade');

            $table->foreignId('folder_id')
                  ->constrained('folders')
                  ->onDelete('cascade');

            $table->unique(['file_id', 'folder_id']);

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
        Schema::dropIfExists('file_folder');
    }
};
