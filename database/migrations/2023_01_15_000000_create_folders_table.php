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
            $table->string              ('path', 255);

            $table->foreignId('owner_id')
                    ->constrained('users')
                    ->onDelete('cascade');

            $table->foreignId('parent_folder_id')
                ->nullable()
                ->constrained('folders')
                ->onDelete('cascade');



            $table->timestamps();
        });

        // add a reference to the parent folder
        Schema::table('folders', function (Blueprint $table) {
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('folders')
                  ->onDelete('cascade');
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
