<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->string  ('name');
            $table->string  ('abbreviation');
            $table->string  ('promotion');
            $table->enum    ('type', ['filiere', 'option']);

            $table->foreignId('parent_filiere_id')
                  ->nullable()
                  ->constrained('filieres')
                  ->onDelete('cascade');

            $table->unique(['name', 'promotion']);

           // create trigger to update the type to option when parent_filiere_id is not null or to filiere when it is null
//              DB::unprepared("
//                CREATE TRIGGER filieres_type_update
//                BEFORE INSERT OR UPDATE ON filieres
//                FOR EACH ROW
//                BEGIN
//                    IF NEW.parent_filiere_id IS NOT NULL THEN
//                        SET NEW.type = 'option';
//                    ELSE
//                        SET NEW.type = 'filiere';
//                    END IF;
//                END
//              ");

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
        Schema::dropIfExists('filieres');

        // drop trigger
        DB::unprepared('DROP TRIGGER filieres_type_update');
    }
};
