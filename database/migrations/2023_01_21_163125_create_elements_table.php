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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->string  ('name');
            $table->timestamps();

            $table->foreignId('filiere_id')
                  ->constrained('filieres')
                  ->onDelete('cascade');

            $table->foreignId('professor_id')
                  ->constrained('users')
                  ->onDelete('no action');

            $table->unique(['name', 'filiere_id']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elements');
    }
};
